<html>
<?php
    require 'crypt.php';
?>


<form action="login.php" method="POST">
<table>
<tr><td>Username<span class="red">*</span></td><td><input type="text" name="fname" value="<?php echo $refill['fname'];?>"></td></tr>
<tr><td>Password<span class="red">*</span></td><td><input type="text" name="lname" value="<?php echo $refill['lname'];?>"></td></tr>
<tr><td colspan="2" style="text-align:center;"><input type="submit" value="Submit Query"></td></tr>
</table>
</form>

</html>