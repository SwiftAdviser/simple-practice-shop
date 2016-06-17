<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css">

    <!-- Put this script tag to the <head> of your page -->
    <script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>

    <script type="text/javascript">
        VK.init({apiId: 5445522});
    </script>

</head>
<body>
<div class="wrapper">
    <h1>Магазын у Ашота</h1>

    <hr>
    <h4>Доступные товары:</h4>
    <div id="vitrina"></div>
    <br clear="all">
    <hr>

    <!-- Put this div tag to the place, where Auth block will be -->
    <div id="vk_auth"></div>

    <script type="text/javascript">

        window.onload = function() {

            if (getCookie("laba_userid") === "") {

                document.getElementById("vitrina").innerHTML = "Войдите, чтобы посмотреть список товаров";

                VK.Widgets.Auth("vk_auth", {
                    width: "200px", onAuth: function (data) {
                        login(data);

                        document.getElementById("vitrina").innerHTML = "";
                        getUserProducts(data['uid']);

                        document.getElementById("vk_auth").innerHTML = "Авторизация успешна, " + data['last_name'] + " " + data['first_name'] +
                            "<input type='submit' onclick='logout()' value='Выйти'>";

                    }
                });
            }
            else
            {
                checkCookie();
                getUserProducts(getCookie("laba_userid"));
            }
        };

        function login(data)
        {
            setCookie("laba_userid",data['uid'],1);
            setCookie("first_name",data['first_name'],1);
            setCookie("last_name",data['last_name'],1);
        }
        function logout()
        {
            setCookie("laba_userid","",-1);
            setCookie("first_name","",-1);
            setCookie("last_name","",-1);
            location.reload();
        }

        function getUserProducts(uid) {

            var xhr = new XMLHttpRequest();

            var params = 'user=' + encodeURIComponent(uid);
            xhr.open("GET", 'http://mineland.net/laba/api.php?' + params, true);
            xhr.setRequestHeader('Content-type', 'text/html; charset=utf-8');

            xhr.onreadystatechange = function () {
                if (xhr.status === 200 && xhr.readyState === 4) {
                    var response = JSON.parse(xhr.responseText);
                    console.log("arra ",response);
                    for (var i = 0; i < response.length; i++) {
                        updateProductButton(response[i]);
                    }
                }
                else if (xhr.readyState === 4) {
                    alert("Error " + xhr.status + ": " + xhr.statusText);
                }
            };

            xhr.send();
        }

        function updateProductButton(product) {
            var el = document.createElement("div");
            el.setAttribute("class", "product");

            var name = document.createElement("div");
            name.setAttribute("class", "name");
            name.innerHTML = product.name;
            var desc = document.createElement("div");
            desc.setAttribute("class", "desc");
            desc.innerHTML = product.desc;
            var button = document.createElement("div");
            button.setAttribute("class", "button");
            button.innerHTML = product.button;

            el.appendChild( name );
            el.appendChild( desc );
            el.appendChild( button );

            document.getElementById("vitrina").appendChild(el );
        }

        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+d.toUTCString();
            document.cookie = cname + "=" + cvalue + "; " + expires;
        }

        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        function checkCookie() {
            var user = getCookie("laba_userid");
            if (user != "") {
                document.getElementById("vk_auth").innerHTML = "Авторизация успешна, " + getCookie("last_name") + " " + getCookie("first_name") +
                    "<input type='submit' onclick='logout()' value='Выйти'>";
            }
        }

    </script>
</div>
</body>
</html>