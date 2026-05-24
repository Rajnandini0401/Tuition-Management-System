<?php
$conn = mysqli_connect("127.0.0.1", "root", "", "otp_login_system", 3307);

$mobile = $_POST['mobile'];

// Check mobile exists
$result = mysqli_query($conn, "SELECT * FROM students WHERE parent_mobile='$mobile'");

if(mysqli_num_rows($result) > 0){

    $otp = rand(100000, 999999);

    // Save OTP
    mysqli_query($conn, "INSERT INTO otp_verification (mobile, otp) VALUES ('$mobile','$otp')");

    // 👉 SMS API
    $fields = array(
        "variables_values" => "$otp",
        "route" => "otp",
        "numbers" => "$mobile",
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode($fields),
      CURLOPT_HTTPHEADER => array(
        "authorization: YOUR_API_KEY",
        "accept: */*",
        "content-type: application/json"
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    // 👉 Testing (optional)
    // echo "OTP is: " . $otp;

    // 👉 Redirect LAST
    header("Location: verify.php?mobile=$mobile");
    exit();

}else{
    echo "Mobile number not registered!";
}
?>