<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'products.php';

// Variabler

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
    foreach ($_POST['amount'] as $key => $value) {
        // Lägg produkt i varukorgen så många gånger som det krävs.
        for ($i = 0; $i < $value; $i++) {
            $cart[] = $products[$key];
        }
    }
    $_SESSION['cart'] = $cart;
}

// Ta bort enskild artikel ur varukorgen
if (isset($_GET['index'])) {
    // Tvätta och rengör.
    $index = filter_var($_GET['index'], FILTER_SANITIZE_NUMBER_INT);

    // Splice:a ut rätt vara ur varukorgen
    array_splice($cart, $index, 1);
    $_SESSION['cart'] = $cart;
}

// Töm varukorgen
if (isset($_POST['emptyCart'])) {
    $cart = [];
    $_SESSION['cart'] = $cart;
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
        <form action="index.php" method="post">
            <ul>
                <?php
                // Lista alla produkter
                foreach ($products as $key => $product) {
                    echo "<li>" . $product['title'] . " " . $product['price'];
                    echo "<input type='number' value='0' name='amount[]'>";
                    echo "</li>";
                }
                ?>
            </ul>
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
            <input type="submit" value="töm varukorgen" name="emptyCart">
        </form>
    </section>
</body>

</html>