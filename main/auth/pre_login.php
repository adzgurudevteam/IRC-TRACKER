<?php
function printContent()
{
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <style>
            ul {
                list-style-type: none;
                margin: 0;
                padding: 0;
                overflow: hidden;
                background-color: #8d8d8d;
            }

            li {
                float: left;
            }

            li a {
                display: block;
                color: white;
                text-align: center;
                padding: 14px 16px;
                text-decoration: none;
            }

            li a:hover:not(.active) {
                background-color: #111;
            }

            .active {
                background-color: #ff1515;
            }
        </style>
    </head>

    <body>
        <nav>
            <ul>
                <!-- <li><a class="active" href="#home">Home</a></li> -->
                <li>
                    <a style="padding: 5px 10px !important;" href="#news">
                        <img style="width: 50px;" src="<?=CDN_URL?>img/sctLogo.png" alt="">
                    </a>
                </li>
                <li><strong style="display: block; padding: 14px 16px;font-weight: bolder; font-size: 25px;">Saha CyberTech Educational Programm</strong></li>
                <li style="float: right !important;"><a class="active" style="padding: 23px 72px !important; font-size:20px;" href="#home">INSTITUTE LOGIN SYSTEM</a></li>
            </ul>
        </nav>
    </body>

    </html>

<?php } ?>