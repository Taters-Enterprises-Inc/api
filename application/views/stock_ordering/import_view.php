<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Import Sample View</title>
</head>
<body>
	<form method="post" enctype="multipart/form-data" action="<?php echo base_url('stock/import-si'); ?>">
		<input type="file" name="file" accept=".xls, .xlsx">
		<input type="submit">
	</form>
</body>
</html>