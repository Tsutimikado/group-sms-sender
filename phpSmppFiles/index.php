<?php

?>

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="style.css" />
</head>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
<section class="mb-4 own">
    <!--Section heading-->
    <h2 class="h1-responsive font-weight-bold text-center my-4">
        Отправка СМС клиентам
    </h2>

    <div class="row justify-content-md-center">
        <!--Grid column-->
        <div class="col-md-9 mb-md-0 mb-5">
            <form id="contact-form" name="contact-form" action="mail.php" method="POST">
                <!--Grid row-->
                <div class="row justify-content-md-center">
                    <div class="col-md-4">
                        <div class="md-form mb-0">
                            <input type="text" id="subject" name="phone" class="form-control" />
                            <label for=" subject" class="">Номер</label>
                        </div>
                    </div>
                </div>
                <!--Grid row-->

                <!--Grid row-->
                <div class="row justify-content-md-center">
                    <!--Grid column-->
                    <div class="col-md-8">
                        <div class="md-form">
                            <textarea type="text" id="message" name="message" rows="10"
                                class="form-control md-textarea"></textarea>
                            <label for=" message">СМС Текст</label>
                        </div>
                    </div>
                </div>
                <!--Grid row-->


                <div class="text-center text-md-center">
                    <a class="btn btn-lg btn-primary bt"
                        onclick="document.getElementById('contact-form').submit();">Отправить</a>
                </div>
                <div class="status"></div>
        </div>
        <!--Grid column-->
    </div>
    </form>
</section>
<!--Section: Contact v.2-->