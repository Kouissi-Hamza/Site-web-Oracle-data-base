<?php

include 'connexion.php';

    $top = $conn->prepare("SELECT IDS, TitreP, count(*) as nbr,s.IDP
                        FROM petition p natural join signature s GROUP BY s.IDP
                        HAVING nbr >= ALL (SELECT count(*) as nbr
                        FROM petition p natural join signature s GROUP BY s.IDP);");
    $top->execute();
    $top = $top->fetch(PDO::FETCH_ASSOC);

    if (!$top) {
        echo json_encode([]);
    } else {
        echo json_encode($top);
    }

?>