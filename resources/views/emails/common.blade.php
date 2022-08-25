<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ $subject }}</title>
    <style>
      img {
        border: none;
        -ms-interpolation-mode: bicubic;
        max-width: 100%;
      }

      body {
        margin: 0 !important;
        padding: 0;
        background-color: #ffffff !important;
        font-family: Arial, sans-serif, 'Open Sans';
        font-size:13px;
      }

      .container{
        margin: 5px auto;
        max-width: 650px;
        border: 1px solid #cfcfcf;
        padding: 10px;
      }

      table, td, th {
        border: 1px solid black;
        padding:5px;
      }

      table {
        border-collapse: collapse;
        width: 100%;
      }

      .header{
          text-align:center;
      }

      .header img{
        margin:0px auto;
      }
      #spacer{min-height:30px;}

      #footer table{
         border-collapse: collapse;
         width: 100%;
      }
      #footer table, td, th {
        border: 0px solid black !important;
        padding:3px;
        font-size: 11px;
      }

      #emailcontent p{
          font-size:13px !important;
      }
    </style>
  </head>
<body>
  <div class="container">
    <div id="spacer"></div>
    <div id="emailcontent">
      {!! $messagecontent !!}
      <br>
    </div>
  </div>
</body>
</html>
