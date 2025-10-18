<nav class="navbar bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="">
        BBSNETWORKS®
      </a>
      <div>
        <ul class="nav justify-content-end">
          <li class="nav-item centrar">
            <a class="nav-link crear" href="../gastos/index.php">Agregar Gasto / Ingreso</a>
          </li>
          <?php
          if($_SESSION['tipo']=='root'){
          ?>
          <li class="nav-item centrar">
            <a class="nav-link lista" href="../table/index.php">Ver Lista de Gastos / Ingresos</a>
          </li>
          <li class="nav-item centrar">
            <a class="nav-link crear" href="../total/index.php">Total de Ingresos</a>
          </li>
          <?php 
          }
          ?>
          <li class="nav-item centrar">
            <a class="nav-link crear" href="../../corte/php/destruir_sesion.php">Salir</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>