<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Inventory Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-gap: 5px;
            margin: 20px;
        }

        .header {
            grid-column: span 12;
            background-color: #d98835;
            text-align: center;
            font-size: 24px;
            padding: 10px;
            color: white;
        }

        .first-production, .last-production, .shop, .sold-pieces, .gold-title, .production-date, .production-details, .all-rest, .gold-types, .stats {
            border: 1px solid black;
            text-align: center;
            padding: 5px;
        }

        .first-production, .last-production, .production-details {
            grid-column: span 3;
        }

        .shop {
            grid-column: span 2;
        }

        .sold-pieces {
            grid-column: span 2;
        }

        .gold-title {
            grid-column: span 4;
            background: linear-gradient(to bottom, #f8b500, #f88c00);
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .production-date {
            grid-column: span 3;
        }

        .production-details {
            grid-column: span 3;
        }

        .all-rest {
            grid-column: span 3;
            background-color: #e0e0ff;
            font-weight: bold;
        }

        .shops-grid {
            grid-column: span 9;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            grid-gap: 5px;
        }

        .shop-box {
            border: 1px solid black;
            padding: 5px;
            background-color: #f2f2f2;
            text-align: center;
            min-height: 40px; /* Space for shop data */
        }

        .gold-types {
            grid-column: span 12;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .gold-type-box {
            padding: 10px;
            background-color: #f2f2f2;
            border: 1px solid black;
            min-height: 30px; /* Space for dynamic gold type data */
        }

        .stats {
            grid-column: span 3;
            padding: 10px;
            border: 1px solid black;
            background-color: #ffebcc;
            min-height: 30px; /* Space for stats data */
        }

        .image-section {
            grid-column: span 12;
            text-align: center;
            padding: 20px;
            min-height: 150px; /* Space for image */
        }

        .footer {
            grid-column: span 12;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            padding: 10px;
            grid-gap: 5px;
        }

        .footer div {
            padding: 10px;
            border: 1px solid black;
            background-color: #f2f2f2;
            text-align: center;
            min-height: 40px; /* Space for footer content */
        }

        .empty-space {
            min-height: 30px;
        }

    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="first-production">First Production<br><span class="empty-space"></span></div>
    <div class="gold-title">GOLD</div>
    <div class="shop">Shop<br><span class="empty-space"></span></div>
    <div class="sold-pieces">Sold Pieces<br><span class="empty-space"></span></div>

    <!-- Production Date -->
    <div class="last-production">Last Production<br><span class="empty-space"></span></div>
    <div class="production-details">Production<br><span class="empty-space"></span></div>

    <!-- All Rest -->
    <div class="all-rest">ALL REST</div>

    <!-- Shops Section -->
    <div class="shops-grid">
        <div class="shop-box">SHOP 5</div>
        <div class="shop-box">Mall of Arabia</div>
        <div class="shop-box">Nasr City</div>
        <div class="shop-box">Zamalek</div>
        <div class="shop-box">Mall of Egypt</div>
        <div class="shop-box">Arkan</div>
        <div class="shop-box">District 5</div>
        <div class="shop-box">U Venues</div>
        <div class="shop-box">SHOP 5</div>
    </div>

    <!-- Gold Types Section -->
    <div class="gold-types">
        <div class="gold-type-box">White Gold</div>
        <div class="gold-type-box">Yellow Gold</div>
        <div class="gold-type-box">Rose Gold</div>
    </div>

    <!-- Stats Section -->
    <div class="stats">Total Production<br><span class="empty-space"></span></div>
    <div class="stats">Total Sold<br><span class="empty-space"></span></div>
    <div class="stats">Remaining<br><span class="empty-space"></span></div>
    <div class="stats">Model<br><span class="empty-space"></span></div>
    <div class="stats">At Workshop<br><span class="empty-space"></span></div>
    <div class="stats">Order Date<br><span class="empty-space"></span></div>

    <!-- Image Section -->
    <div class="image-section">
        <img src="path_to_your_image" alt="Gold Item Image" width="150">
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Sold<br><span class="empty-space"></span></div>
        <div>Rest<br><span class="empty-space"></span></div>
        <div>Description<br><span class="empty-space"></span></div>
    </div>
</div>

</body>
</html>
