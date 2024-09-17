<?php require_once('IndexHandler.php'); require_once('layouts/header.php'); ?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="files/DataTables/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <link rel="shortcut icon" href="images/favicon.ico">
    <title> SMS Panel</title>
  </head>
  <body>
<?php headerL(0)?>
    <main>
      <div class="container pt-3">
      <table id="smslist" class="display">
        <thead>
          <tr>
            <th scope="col">Дата</th>
            <th scope="col">Назначение</th>
            <th scope="col">Источник</th>
            <th scope="col">Содержание</th>
            <th scope="col">Размер смс</th>
          </tr>
        </thead>
          <tbody>
          <?php foreach($result as $res)
            {
              echo "<tr>";
              echo "<td>".$res['date']."</td>  
              <td>".$res['dst']."</td>
              <td>".$res['src']."</td>
              <td>".mb_convert_encoding($res['content'], 'UTF-8', 'UCS-2')."</td>
              <td>".(intdiv(strlen(mb_convert_encoding($res['content'], 'UTF-16', 'UCS-2')), 67)+1)."</td>
              </tr>";
            }
			
            ?>
          </tbody>
        </table>
      </div>
    </main>
    <footer>
    
    </footer>
    

  </body>
  
  <script  src="js/jquery-3.5.1.min.js"></script>
  <script type="text/javascript" src="files/DataTables/datatables.min.js"></script>
  <script>
    $(document).ready(function() {
        $('#smslist').DataTable(
        {
		   "order": [[ 0, "desc" ]],
           "language": {
             "processing": "Подождите...",
             "search": "Поиск:",
             "lengthMenu": "Показать _MENU_ записей",
             "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
             "infoEmpty": "Записи с 0 до 0 из 0 записей",
             "infoFiltered": "(отфильтровано из _MAX_ записей)",
             "infoPostFix": "",
             "loadingRecords": "Загрузка записей...",
             "zeroRecords": "Записи отсутствуют.",
             "emptyTable": "В таблице отсутствуют данные",
             "paginate": {
               "first": "Первая",
               "previous": "Предыдущая",
               "next": "Следующая",
               "last": "Последняя"
             },
             "aria": {
               "sortAscending": ": активировать для сортировки столбца по возрастанию",
               "sortDescending": ": активировать для сортировки столбца по убыванию"
             },
             "select": {
               "rows": {
                 "_": "Выбрано записей: %d",
                 "0": "Кликните по записи для выбора",
                 "1": "Выбрана одна запись"
               }
             }
          }
        });
    } ); 
  </script>
</html>
