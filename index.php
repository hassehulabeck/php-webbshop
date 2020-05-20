<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Om $_SESSION['products'] är "satt", placera det i $cart.
if (isset($_SESSION['products'])) {
    $products = $_SESSION['products'];
} else {
    include 'products.php';
}

function updateSession($cart, $products)
{
    $_SESSION['cart'] = $cart;
    $_SESSION['products'] = $products;
}


// Variabler

// Hämta alla värden ur egenskapen/kolumnen format.
$categories = array_unique(array_column($products, "format"));


// Hämta filteringsquery från URL
if (isset($_GET['filter'])) {
    // Tvätta och rengör.
    $filter = filter_var($_GET['filter'], FILTER_SANITIZE_STRING);
    $_SESSION['filter'] = $filter;
    $filteredProducts = array_filter($products, function ($product) use ($filter) {
        return $product['format'] == $filter;
    });
} else {
    $filteredProducts = $products;
}


// Om cart är "satt", placera det i $cart.
if (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
} else {
    // Definiera $cart som en tom varukorg/array.
    $cart = [];
}
$cartValue = 0;

// Lägg till i varukorgen
if (isset($_POST['addToCart'])) {
    foreach ($_POST['amount'] as $id => $antal) {
        // Hitta index för produkten med id = $id.
        $index = array_search($id, array_column($products, 'id'));

        // Amount är antingen så många exemplar det finns av en produkt... 
        $amount = $products[$index]['lagersaldo'];

        // ...eller så många som användaren vill ha.
        if ($products[$index]['lagersaldo'] > $antal) {
            $amount = $antal;
        }
        // Lägg produkt i varukorgen så många gånger som det krävs.
        // Uppdatera också lagersaldot.
        for ($i = 0; $i < $amount; $i++) {
            $cart[] = $products[$index];
            $products[$index]['lagersaldo']--;
        }
    }
    updateSession($cart, $products);
}

// Ta bort enskild artikel ur varukorgen
if (isset($_GET['index'])) {
    // Tvätta och rengör.
    $index = filter_var($_GET['index'], FILTER_SANITIZE_NUMBER_INT);

    // Splice:a ut rätt vara ur varukorgen - array_splice returnerar en array, 
    // vi vill ha första elementet, därav nollan.
    $removedProduct = array_splice($cart, $index, 1)[0];

    // Kolla titeln, jämför med title i $products.
    $index = array_search($removedProduct['title'], array_column($products, 'title'));
    // Använd det index-värdet och öka lagerstatus med 1.
    $products[$index]['lagersaldo']++;

    updateSession($cart, $products);
}

// Töm varukorgen
if (isset($_POST['emptyCart'])) {

    // I och med egenskapen lagersaldo i products, bör vi föra tillbaka produkterna när vi tömmer varukorgen.
    foreach ($cart as $product) {
        // Kolla titeln, jämför med title i $products.
        $index = array_search($product['title'], array_column($products, 'title'));
        // Använd det index-värdet och öka lagerstatus med 1.
        $products[$index]['lagersaldo']++;
    }

    $cart = [];
    updateSession($cart, $products);
}


?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webbshop</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <section>
        <h1>Alla produkter</h1>
        <h2>Filtrera</h2>
        <?php
        foreach ($categories as $category) {
            echo "<a href='index.php?filter=$category' >$category</a> - ";
        }
        ?>
        <a href='index.php'>Alla</a>
        <form action="index.php" method="post">
            <table>
                <tr>
                    <th>Title
                    <th>Pris
                    <th>Antal
                        <?php
                        // Lista alla produkter
                        foreach ($filteredProducts as $key => $product) {
                            if ($product['lagersaldo'] > 0) {
                                echo "<tr><td>" . $product['title'] . "<td>" . $product['price'];
                                $id = $product['id'];
                                echo "<td><input type='number' value='0' name='amount[$id]'>";
                            } else {
                                echo "<tr><td colspan=3>" . $product['title'] . " är tyvärr slut i lager.";
                            }
                        }
                        ?>
            </table>
            <input type="submit" name="addToCart" value="Lägg i varukorgen">
        </form>
    </section>
    <section>
        <h1>Varukorg</h1>
        <ul>
            <?php
            foreach ($cart as $key => $product) {
                echo "<li>" . $product['title'] . " " . $product['price'];
                echo "<a href='index.php?index=$key'> x </a></li>";
                // Räkna ut värdet på varukorgen
                $cartValue += $product['price'];
            }
            ?>
        </ul>
        <?php
        echo "Total kostnad: " . $cartValue;
        ?>
        <form action="index.php" method="post">
            <input type="submit" value="Töm varukorgen" name="emptyCart">
        </form>
    </section>
</body>

</html>