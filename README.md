# Reserva de taules en un restaurant

<h3><strong>ESTRUCTURA DEL RESTAURANT</strong></h3>
<ul>
<li>El restaurant tindrà diferents "sales". En concret, tindrà 3 terrasses, 2 menjadors i 4 sales privades. La distribució de taules de cada sala és lliure a definir pels membres del grup. </li>
</ul>
  <hr>
<h3><strong>FUNCIONAMENT DE L'APLICACIÓ</strong></h3>
  <ul>
<li>Els usuaris que fan servir l'aplicació en aquest projecte seran els cambrers del restaurant, que han de poder veure la disponibilitat de taules i cadires que té cada taula, així com les sales de té el restaurant i la capacitat total de cada una d’elles. </li>
<li>Els cambrers seran els encarregats de marcar com a ocupades les taules quan el client arriba al restaurant. Això vol dir que un recurs (o taula) estarà lliure o ocupat en tot moment. No es reserva per a un dia i una hora.  </li>
<li>La ocupació d'un recurs va associada a un usuari cambrer, per tant s'ha de poder fer login/logout en el sistema de forma prèvia a l’ocupació o l’alliberament del recurs. </li>
<li>Un cop s'allibera el recurs (el client ha acabat de dinar o sopar), el cambrer és qui el marca com a lliure. </li>
<li>Els usuaris ja estan creats a la base de dades (com si vinguessin d'una altra BD), és a dir, no calen formularis d'alta/baixa/modificació d'usuaris. </li>
  </ul>
    <hr>
<h3><strong>HISTÒRIC</strong></h3>
<ul>
<li>S'haurà de guardar el dia i la hora a la que s'ha agafat un recurs i a la que s'ha alliberat.  </li>
<li>El sistema haurà de permetre visualitzar les ocupacions que s'han realitzat dels recursos, filtrant per recurs i ubicació/sala del recurs, i així veure si un recurs concret s'ha fet servir molt, poc...  </li>
</ul>
<hr>
<h3><strong>Ampliació</strong></h3>
<ul>
<li>una opció de reservar un recurs (cambrers) en un dia i una franja horaria específics</li>
<li>una opció de fer un CRUD (per l’administrador de la web) d’usuaris tals com cambrers, gerent, personal manteniment, etc.</li>
<li>una opció de fer un CRUD (per l’administrador de la web) de recursos tals com sales, taules, cadires, etc.</li>
</ul>

