<?php
    include('../includes/header.php');
?>
    <script type="module" src="../js/employee.js"></script>
</head>
<body>
    <form id="new_employee">
        <input type="text" name="nombre" id="nombre">
        <input type="text" name="apellido" id="apellido">
        <input type="text" name="documento" id="documento">
        <input type="tel" name="telefono" id="telefono">
        <input type="text" name="direccion" id="direccion">
        <input type="text" name="email" id="email">
        <input type="submit" value="Registrar">
    </form>
</body>
</html>