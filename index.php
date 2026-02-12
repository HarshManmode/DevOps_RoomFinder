<?php include "config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PG Rental - Find Your Perfect Room</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-building"></i>
                </div>
                PG Rental
            </a>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="my_bookings.php" class="nav-link">
                        <i class="fas fa-calendar-check"></i> My Bookings
                    </a>
                    <a href="logout.php" class="btn btn-outline">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="fade-in">
                <h1 class="text-center mb-4">
                    <i class="fas fa-home text-primary"></i>
                    Find Your Perfect PG Room
                </h1>
                <p class="text-center text-muted mb-4">
                    Discover comfortable and affordable rental rooms across the city
                </p>

                <?php 
                // Handle success/error messages
                if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
                    $name = isset($_GET['name']) ? htmlspecialchars(urldecode($_GET['name'])) : 'User';
                    echo '<div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Successfully logged out, ' . $name . '! We hope to see you again soon.
                          </div>';
                }
                ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-user-circle"></i>
                        Welcome back! Browse our available rooms and find your next home.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <i class="fas fa-lightbulb"></i>
                        New here? <a href="register.php" class="text-primary">Register now</a> to start booking your perfect room!
                    </div>
                <?php endif; ?>

                <?php
                $result = mysqli_query($conn, "SELECT * FROM rooms ORDER BY price ASC");
                $room_count = mysqli_num_rows($result);
                
                if ($room_count > 0):
                ?>
                    <div class="rooms-grid">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="room-card fade-in">
                                <div class="room-image">
                                    <i class="fas fa-bed fa-3x"></i>
                                </div>
                                <div class="room-content">
                                    <h3 class="room-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <div class="room-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($row['location']); ?>
                                        </span>
                                        <span class="meta-item">
                                            <i class="fas fa-bed"></i>
                                            <?php echo htmlspecialchars($row['bedrooms']); ?> Bedrooms
                                        </span>
                                        <span class="meta-item">
                                            <i class="fas fa-bath"></i>
                                            <?php echo htmlspecialchars($row['bathrooms']); ?> Bathrooms
                                        </span>
                                    </div>
                                    <p class="room-price">₹<?php echo number_format($row['price']); ?> <span class="text-muted">/ month</span></p>
                                    <div class="room-actions">
                                        <a href="room.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <a href="room.php?id=<?php echo $row['id']; ?>" class="btn btn-outline">
                                                <i class="fas fa-calendar-plus"></i> Book Now
                                            </a>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-outline">
                                                <i class="fas fa-sign-in-alt"></i> Login to Book
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        No rooms available at the moment. Please check back later or contact us for more information.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer style="margin-top: auto; padding: 2rem 0; border-top: 1px solid var(--border-color); color: var(--text-secondary);">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> PG Rental. All rights reserved.</p>
            <p class="text-muted">Built with ❤️ for infinityfree hosting</p>
        </div>
    </footer>

    <script>
        // Add smooth animations and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to room cards
            const roomCards = document.querySelectorAll('.room-card');
            roomCards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-5px) scale(1.02)';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add fade-in animation to elements
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.room-card').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
