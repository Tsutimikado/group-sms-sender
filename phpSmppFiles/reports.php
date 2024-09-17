<?php  require_once('layouts/header.php'); ?>

<html>
  <head>
    <link rel="stylesheet" type="text/css" href="files/DataTables/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <link rel="shortcut icon" href="images/favicon.ico">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> 
    <script src="js/reports.js"></script>
    <title> Reports</title>
  </head>
  <body>
  <?php headerL(0)?>
    <div class="container">
      <h2>Отчеты</h2>
      <p>Страница отчетов для смс</p>
      <ul class="nav nav-pills">
        <li class="active"><a data-toggle="pill" href="#home">Простой</a></li>
        <li class="disabled"><a class="disabled" data-toggle="pill" href="#menu1" >Тип2</a></li>
        <li class="disabled"><a data-toggle="pill" href="#menu2" disabled>Тип3</a></li>
        <li class="disabled"><a data-toggle="pill" href="#menu3" disabled>Тип4</a></li>
      </ul>
      
      <div class="tab-content">
        <div id="home" class="tab-pane fade in active" style="background:#fafafa">
          <h3>Обычный отчет</h3> 
            <div class="form-group">
		      <label for="date1"> От </label> <input type="date" name="date1" id="date1" >				
              <label for="date2"> До </label> <input type="date" name="date2" id="date2">
              <input class="btn btn-primary ml-4" value="Сгенерировать" type="button" onclick="SimpleReport()">
			  <input name="type" value="1" hidden>
		    </div> 
		  <table class="table" id="table1">
            <thead>
              <tr>
                <th scope="col">Период</th>
                <th scope="col">Источник</th>
                <th scope="col">Количество смс</th> 
              </tr>
            </thead>
            <tbody id="table1body">
			</tbody>
		  </table>
		  
        </div>
        <div id="menu1" class="tab-pane fade">
          <h3>Тип2</h3> 
        </div>
        <div id="menu2" class="tab-pane fade">
          <h3>Тип3</h3> 
        </div>
        <div id="menu3" class="tab-pane fade">
          <h3>Тип4</h3> 
        </div>
      </div>
    </div>
  </body>
</html>