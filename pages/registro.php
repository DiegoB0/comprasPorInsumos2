<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Registrar</title>
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Main Style Css -->
    <link rel="stylesheet" href="../assets/css/registro.css"/>
</head>
<body class="form-v9">
	<div class="page-content">
		<div class="form-v9-content" style="background-color: #FFF;">
		
			<form class="form-detail" action="../controllers/registro.php" method="post">
				<h2>Registrar</h2>
				<div class="form-row-total">
					<div class="form-row">
						<input type="text" name="nombre" id="nombre" class="input-text" placeholder="Nombre" required>
					</div>
					<div class="form-row">
						<input type="text" name="email" id="email" class="input-text" placeholder="Correo electrónico" required pattern="[^@]+@[^@]+.[a-zA-Z]{2,6}">
					</div>
				</div>
				<div class="form-row-total">
					<div class="form-row">
						<select name="priv" id="priv" class="input-text" style="width: 108%" required>
							<option value="estandar">Estándar</option> 
	                  		<option value="admin">Administrador</option>
						</select>
					</div>
					<div class="form-row">
						<input type="password" name="pass" id="pass" class="input-text" placeholder="Contraseña" required>
					</div>
				</div>
				<div class="form-row-last">
					<input type="submit" name="registrar" class="register" value="Registrar">
				</div>
			</form>
		</div>
	</div>
</body>
</html>