<html>
	<head>
		<meta charset = "utf-8" />
		<style>
			body{
				margin: 0;
				padding: 0;
			}
			input{
				margin-left: 20px;
				margin-top: 20px;
				width: 450px;
				height: 30px;
				padding-left: 15px;
			}
		</style>
		
	</head>
	<body>
		<form action = "crawler-handle.php" method = "get">
			<input type = "text" name = "para" placeholder = "Search..." onkeyup = "search(this.value)" />
		</form>
		<div id = "result-div">
		</div>
	</body>
</html>