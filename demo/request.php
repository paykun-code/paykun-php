
<!DOCTYPE html>
<html>
<head>
	<title>PayKun Demo</title>
</head>
<body>
<center>   
	<table border="1">

<form method="POST" action="submit.php">
	<h1>PayKun Demo</h1>
		

	<tr>
		<td><label>full name</label></td>
		<td><input type="text" name="full_name"></td>
	</tr>

	<tr>
		<td><label>product name</label></td>
		<td><input type="text" name="product_name"></td>
	</tr>
	
	<tr>
		<td><label>email</label></td>
		<td><input type="email" name="email"></td>
	</tr>

	<tr>
		<td><label>amount</label></td>
		<td><input type="text" name="amount"></td>
	</tr>

	<tr>
		<td><label>contact No</label></td>
		<td><input type="text" maxlength="10" name="contact"></td>
	</tr>

	<tr>
		<td><label>country</label></td>	
		<td><input type="text" name="country" value="india"></td>
	</tr>
	
	<tr>
		<td><label>state</label></td>	
		<td><input type="text" name="state"></td>
	</tr>
		
	<tr>
		<td><label>city</label></td>
		<td><input type="text" name="city"></td>
	</tr>

	<tr>
		<td><label>postal code</label></td>
		<td><input type="text" name="postalcode" maxlength="6"></td>
	</tr>

	<tr>
		<td><label>address</label></td>
		<td><input type="text" name="address"></td>
	</tr>
	
	<tr>
		<td colspan="2">
	 		<center colspan="2"><input type="submit" value="submit"></center>
		</td>
	</tr>

</form>
</table>
</center>

</body>
</html>
