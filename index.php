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

// Kolla om användaren har tryckt på...
if (isset($_POST)) {

    if (isset($_POST['emptyCart'])) {
        $cart = [];
        $_SESSION['cart'] = $cart;
    } else {
        // Hämta index-värdet.
        foreach ($_POST as $key => $value) {
            // Lägg produkt i varukorgen
            $cart[] = $products[$key];
            $_SESSION['cart'] = $cart;
        }
    }
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
                    echo "<input type='submit' value='Lägg till i varukorg' name='$key'>";
                    echo "</li>";
                }
                ?>
            </ul>
        </form>
    </section>
    <section>
        <h1>Varukorg</h1>
        <ul>
            <?php
            foreach ($cart as $product) {
                echo "<li>" . $product['title'] . " " . $product['price'];
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