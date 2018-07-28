<?php

//diretorio dos arquivos para ler
if ($handle = opendir('data/in/')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $filename = explode(".", $file);
            analisaArquivo($filename[0]);
        }
    }
    closedir($handle);
}

function analisaArquivo($name){

    $fp = file("./data/in/".$name.'.dat'); // $fp conterá o handle do arquivo que abrimos
    $idVendedor = 1;
    $idCliente = 1;
    $idVenda = 1;
    $vendedores = [];
    $clientes = [];
    $vendasRef = [];
    $vendas = [];


    foreach ($fp as $line) {

        $pieces = explode(",", $line);

        if($pieces[0] == '1'){
            array_push($vendedores, array('type' => $pieces[0], 'id' => $idVendedor++, 'cpf' => $pieces[1], 'name' => $pieces[2], 'salary'=>$pieces[3], 'vendaTotal'=>0));
        } elseif ($pieces[0] == '2'){
            array_push($clientes, array('type' => $pieces[0], 'id' => $idCliente++, 'cnpj' => $pieces[1], 'name' => $pieces[2], 'Business Area'=>$pieces[3]));
        } elseif ($pieces[0] == '3') {
            array_push($vendasRef, array('type' => $pieces[0], 'id' => $idVenda, 'array' => $pieces[2], 'Salesman'=>$pieces[3]));
            $arrayVenda = preg_replace('/[^A-Za-z0-9\-]/', ' ', $pieces[2]);
            $piecesArray = explode(" ", $arrayVenda);
            array_push($vendas, array('id' => $idVenda++, 'item' => $piecesArray[1],'idItem' => $piecesArray[2],'quant' => $piecesArray[3],'price' => $piecesArray[4]));
        }

    }

    //  Aplica o valor total das vendas para cada vendedor;
        foreach($vendas as $venda){
            foreach($vendasRef as $ref){
                if($venda['id'] == $ref['id']){
                    foreach($vendedores as $key => $vendedor){
                        if($ref['Salesman'] == $vendedor['id']){
                            $vendedores[$key]['vendaTotal'] = $vendedores[$key]['vendaTotal'] + $venda['price'];
                        }
                    }
                }
            }
        }

        $min = min(array_column($vendedores, 'vendaTotal'));
        $piorVendedor = array_search($min, array_column($vendedores, 'vendaTotal'));

    //  Realiza a soma dos salários do vendedores;
        $sum = 0;
        foreach($vendedores as $vendedor){
            $sum += $vendedor['salary'];
        }

        $max = max(array_column($vendas, 'price'));
    //  Função para retornar o indice do maior valor de venda;
        $key = array_search($max, array_column($vendas, 'price'));

//        echo 'Número de clientes: ' .count($clientes) .'<br>'.'Número de vendedores: ' .count($vendedores) .'<br>'. 'Média salárial: ' .$sum/count($vendedores) .'<br>' . 'ID maior venda: ' . $vendas[$key]['id'] .'<br>' . 'Pior vendedor: ' .$vendedores[$piorVendedor]['name'] ;

    $fpo = fopen("./data/out/".$name.'.done.dat', "w");
    // grava a string no arquivo. Se o arquivo não existir ele será criado
    fwrite($fpo, 'Número de clientes: ' .count($clientes) . PHP_EOL .'Número de vendedores: ' .count($vendedores) . PHP_EOL . 'Média salárial: ' .$sum/count($vendedores) . PHP_EOL . 'ID maior venda: ' . $vendas[$key]['id'] . PHP_EOL . 'Pior vendedor: ' .$vendedores[$piorVendedor]['name']);
    fclose($fpo);
}
?>