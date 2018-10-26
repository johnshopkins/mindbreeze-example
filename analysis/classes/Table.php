<?php

class Table
{
  public function display($data)
  {
    echo '<style>td { word-break: break-all; }</style>';
    echo '<table>';

    foreach ($data as $row) {
      echo '<tr>';
      foreach ($row as $cell) {
        echo '<td>' . $cell . '</td>';
      }
      echo '</tr>';
    }

    echo '</table>';
  }
}
