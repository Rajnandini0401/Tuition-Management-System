<form method="POST" action="verify_otp.php">
    <input type="hidden" name="mobile" value="<?php echo $_GET['mobile']; ?>">
    <input type="text" name="otp" placeholder="Enter OTP" required>
    <button type="submit">Verify OTP</button>
</form>