<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Currency Converter App in JavaScript | CodingNepal</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  </head>
  <body>
    <div class="wrapper">
      <header>Currency Converter</header>
      <form action="#" method="post">
        <div class="amount">
          <p>Enter Amount</p>
          <input type="text" value="<?php echo isset($_POST['amount']) ? $_POST['amount'] : '' ?>" name=amount>
        </div>
        <div class="drop-list">
          <div class="from">
            <p>From</p>
            <div class="select-box">
              <img src="https://flagcdn.com/48x36/us.png" alt="flag">
              <select name=basecurr <?php echo (isset($_POST['basecurr']) && $_POST['basecurr'] === 'option1') ? 'selected' : ''; ?>> <!-- Options tag are inserted from JavaScript --> </select>
            </div>
          </div>
          <div class="icon"><i class="fas fa-exchange-alt"></i></div>
          <div class="to">
            <p>To</p>
            <div class="select-box">
              <img src="https://flagcdn.com/48x36/np.png" alt="flag">
              <select name=targetcurr > <!-- Options tag are inserted from JavaScript --> </select>
            </div>
          </div>
        </div>
        <div class="exchange-rate">Getting exchange rate...</br>
        <?php 

         require_once 'debug.php';

        if($_SERVER["REQUEST_METHOD"] == "POST") {

          $db_connection=mysqli_connect("localhost", "root", "#Honey@214", "currency");

          $baseCurrency = $_POST["basecurr"];
          $targetCurrency = $_POST["targetcurr"];
          $amount = $_POST["amount"];
           
        if($db_connection)
        {
           
          $newTime=date('Y-m-d H:i:s',strtotime('-30 minutes'));

          $query= "SELECT conversion_rate FROM conversion WHERE base_currency='$baseCurrency' AND target_currency='$targetCurrency'  AND time_stamp>='$newTime'" ;
      
          $result=mysqli_query($db_connection, $query);
 
          if($result && mysqli_num_rows($result)>0)
          {

            $row=mysqli_fetch_assoc($result);
            $conversion_result=$row['conversion_rate']*$amount;
            mysqli_close($db_connection);
  
          }

         else
        {  

            $curl_handle = curl_init();
              
            $url = "https://v6.exchangerate-api.com/v6/ad3139838e94cb34a35372d9/pair/$baseCurrency/$targetCurrency/$amount";


            curl_setopt($curl_handle, CURLOPT_URL, $url);

            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);

            $response_data = json_decode($curl_data);
            $conversion_result=$response_data->conversion_result;

            $conversion_rate=$response_data->conversion_rate;
            
            $query="SELECT conversion_rate FROM conversion WHERE base_currency='$baseCurrency' AND target_currency='$targetCurrency'";


            $result=mysqli_query($db_connection,$query);

            if($result && mysqli_num_rows($result)>0)
            {
              
              $time=date('Y-m-d H:i:s');
              $query="UPDATE conversion SET time_stamp='$time',coversion_rate='$conversion_rate' WHERE base_currency='$basecurr' AND target_currency='$targetcurr'";
            }
             
            else
            {
              
             $query="INSERT INTO conversion(base_currency,target_currency,conversion_rate) VALUES('$baseCurrency','$targetCurrency','$conversion_rate')";
            }
        
            mysqli_query($db_connection, $query);
            mysqli_close($db_connection);
        }


        echo $conversion_result;
         

      }
    }
        
  
        ?>
        </div>
        <button>Get Exchange Rate</button>
      </form>
    </div>



    
    <script src="country-list.js"></script>
    <script src="script.js"></script>
  </body>
</html>