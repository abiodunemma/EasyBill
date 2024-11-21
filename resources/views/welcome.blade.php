<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with Paystack</title>
</head>
<body>
    <h1>Make a Payment</h1>

    <form action="{{ route('paystack.initialize') }}" method="POST">
        @csrf
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="amount">Amount:</label>
        <input type="number" name="amount" required><br><br>

        <button type="submit">Pay Now</button>
    </form>
</body>
</html>
