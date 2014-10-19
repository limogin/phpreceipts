Receipts 
========

Script para generar recibos de cuotas para comunidades de vecinos.

# Instalación

Es necesario disponer de un intérprete php versión 4 o superior. Por ejemplo procedente de una instalación XAMPP (https://www.apachefriends.org/es/index.html)
 
Es neceario disponer de la librería fpdf http://www.fpdf.org/
 
Descargar el paquete y copiarlo en la carpeta ./fpdf/ 


# Configuración 


Son necesarios dos archivos, config.txt con los datos de la comunidad y users.txt con los datos de los vecinos.

config.txt
La primera línea debe de constar el título de la propiedad y en la segunda los datos de dirección.

Ejemplo:

Comunidad de propietarios de la fincha de la calle de la piruleta
Calle de la piruleta, num.32, bla, bla, bla 

users.txt
Cada línea contiene los datos de un vecino, nombre, piso o puerto e importe. Separados por punto y coma.

Ejemplo:

Mi Nombre 1 ; 1o 1a; 32
Mi Nombre 2 : 1o 2a; 32 
 

# Forma de Uso

Para generar una aportación extraordinaria puntual 
php receipt.php aportation [fecha] [importe] [desde-num-recibo] [concepto] 

Ejemplo:
php receipt.php aportation 2014-04-05 250 7 "Arreglo Escalera"

Para generar una aportación ordinaria mensual 
php receipt.php month [year]-[month] [from-receipt-number]

Ejemplo:

php receipt.php 2014-10 12

Para generar todas las aportaciones del año
php receipt.php year [year] [from-receipt-number]

Ejemplo:

php receipt.php 2014 34 

Tienes que considerar que si generas todas las aportaciones del año y en algún momento
hay que generar aportaciones extraordinarias, es necesario recalcular de nuevo los números
de recibo. Por eso es más recomendable generar los recibos en el momento en el que
se genere la necesidad de emitirlos.


# Licencia 

The MIT License (MIT)

Copyright (c) 2014 Victor

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 


