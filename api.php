<?php
ini_set('display_errors', 1);


class Log{
    //Log::write('message', 'file');
    static function write($mess="", $name="log"){
        if(strlen(trim($mess)) < 2){
            return fasle;
        }
        if(preg_match("/^([_a-z0-9A-Z]+)$/i", $name, $matches)){
            $file_path = $name.'.txt';
            $text = htmlspecialchars($mess)."\r\n";
            $handle = fopen($file_path, "a");
            @flock ($handle, LOCK_EX);
            fwrite ($handle, $text);
            fwrite ($handle, "==============================================================\r\n\r\n");
            @flock ($handle, LOCK_UN);
            fclose($handle);
            return true;
        }
        else{
            return false;
        }
    }
}

require_once "config.php";
require_once "LiqPay.php";

if (isset($_REQUEST['user'])) {
    $user = htmlspecialchars(addslashes($db->real_escape_string($_REQUEST['user'])));

    $products = $db->query("SELECT * FROM `products`");

    // get user's products
    $stmt = $db->stmt_init();
    $stmt = $db->prepare("SELECT `id` FROM `user_products` WHERE `user`=?");

    if (!$stmt) {
        throw new Exception($db->errno);
    }

    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($user_id);

    $user_ids = array();
    while ($stmt->fetch())
    {
        $user_ids[] = $user_id;
    }

    $stmt->close();
    $db->close();

    $i = 0;
    $result = array();
    while ($product = $products->fetch_assoc()) {
        $result[$i]['name'] = $product['name'];
        $result[$i]['desc'] = $product['desc'];
        $result[$i]['price'] = $product['price'];

        if (in_array($product['id'], $user_ids)) {
            $result[$i]['button'] = "Куплено!";
        } else {
            $liqpay = new LiqPay($public_key, $private_key);
            $result[$i]['button'] = $liqpay->cnb_form(array(
                'version' => '3',
                'action' => 'pay',
                'amount' => $product['price'],
                'currency' => 'UAH',
                'description' => $product['desc'],
                'product_name' => $product['name'],
                'product_description' => $product['desc'],
                'customer' => $user,
                'info' => $product['id'],
                'sandbox' => '1',
            ));
        }
        $i++;
    }

    echo json_encode($result);
}
else if ($_REQUEST['data'])
{
    $income_data = $_REQUEST['data'];

    $sign = base64_encode( sha1(
        $private_key .
        $income_data .
        $private_key
        , 1 ));

    if ($_REQUEST['signature'] === $sign)
    {
        $decoded = base64_decode($income_data);
        $data = json_decode($decoded);


        $stmt = $db->stmt_init();
        $stmt = $db->prepare("INSERT INTO `user_products` (`user`,`id`) VALUES ( ? , ? )");

        if (!$stmt) {
            Log::write("DB exception:" . $db->errno . $db->error);
            throw new Exception($db->errno);
        }
        $stmt->bind_param("si",$data->{'customer'},$data->{'info'});

        $stmt->execute();
        $stmt->close();
        $db->close();
    }
    else {

        Log::write("Income sign:" . $_REQUEST['signature']);
        Log::write("My sign: " . $sign);
    }

}