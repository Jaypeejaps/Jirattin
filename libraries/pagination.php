<?php

function paulpagination($Activepage, $Start, $End, $Totalpages)
{
    $pagination = '<div>';

    $pagination .= '<a href="?Activepage=1" class="' . ($Activepage == 1 ? 'active' : '') . '">First</a>';

    $pagination .= '<a href="?Activepage=' . ($Activepage - 1) . '" class="' . ($Activepage == 1 ? 'disabled' : '') . '" style="border: none; width: 40px;"><i class="bi bi-arrow-left"></i></a>';

    for ($i = $Start; $i <= $End; $i++) {
        $pagination .= '<a href="?Activepage=' . $i . '" class="' . ($i == $Activepage ? 'active' : '') . '">' . $i . '</a>';
    }

    $pagination .= '<a href="?Activepage=' . ($Activepage + 1) . '" class="' . ($Activepage == $Totalpages ? 'disabled' : '') . '" style="border: none; width: 40px;"><i class="bi bi-arrow-right"></i></a>';

    $pagination .= '<a href="?Activepage=' . $Totalpages . '" class="' . ($Activepage == $Totalpages ? 'active' : '') . '">Last</a>';

    $pagination .= '</div>';

    return $pagination;
}
