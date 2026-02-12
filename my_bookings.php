<?php
include "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user information
$user_query = mysqli_query($conn, "SELECT name FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($user_query);

// Get bookings with room details
$bookings_query = mysqli_query($conn, "
    SELECT b.*, r.title, r.location, r.price, r.bedrooms, r.bathrooms 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = $user_id 
    ORDER BY b.from_date DESC
");

$bookings = [];
while ($row = mysqli_fetch_assoc($bookings_query)) {
    $bookings[] = $row;
}

// Get statistics
$total_bookings = count($bookings);
$upcoming_bookings = 0;
$completed_bookings = 0;

$current_date = date('Y-m-d');
foreach ($bookings as $booking) {
    if ($booking['to_date'] >= $current_date) {
        $upcoming_bookings++;
    } else {
        $completed_bookings++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - PG Rental</title>
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
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i> Browse Rooms
                </a>
                <a href="logout.php" class="btn btn-outline">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="fade-in">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <div>
                        <h1 style="margin-bottom: 0.5rem;">
                            <i class="fas fa-calendar-check text-primary"></i>
                            My Bookings
                        </h1>
                        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</p>
                    </div>
                    <div style="text-align: right;">
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <div style="background: rgba(59, 130, 246, 0.1); padding: 1rem; border-radius: var(--radius-md); border-left: 4px solid var(--primary-color);">
                                <h4 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Total Bookings</h4>
                                <p style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--primary-color);"><?php echo $total_bookings; ?></p>
                            </div>
                            <div style="background: rgba(16, 185, 129, 0.1); padding: 1rem; border-radius: var(--radius-md); border-left: 4px solid var(--accent-color);">
                                <h4 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Upcoming</h4>
                                <p style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--accent-color);"><?php echo $upcoming_bookings; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (empty($bookings)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        You haven't made any bookings yet. <a href="index.php" class="text-primary">Browse our available rooms</a> to get started!
                    </div>
                    
                    <div style="text-align: center; margin-top: 3rem;">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-search"></i> Find Rooms
                        </a>
                    </div>
                <?php else: ?>
                    <div class="bookings-container fade-in">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                            <h2 style="margin: 0;">
                                <i class="fas fa-list"></i> Booking History
                            </h2>
                            <div style="display: flex; gap: 1rem;">
                                <span style="background: rgba(59, 130, 246, 0.1); padding: 0.5rem 1rem; border-radius: var(--radius-md); color: var(--primary-color); font-weight: 600;">
                                    <i class="fas fa-calendar-alt"></i> <?php echo $upcoming_bookings; ?> Upcoming
                                </span>
                                <span style="background: rgba(107, 114, 128, 0.1); padding: 0.5rem 1rem; border-radius: var(--radius-md); color: #6b7280; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> <?php echo $completed_bookings; ?> Completed
                                </span>
                            </div>
                        </div>

                        <div>
                            <?php foreach ($bookings as $booking): ?>
                                <?php
                                $is_upcoming = $booking['to_date'] >= $current_date;
                                $status_class = $is_upcoming ? 'text-accent' : 'text-muted';
                                $status_icon = $is_upcoming ? 'fas fa-clock' : 'fas fa-check-circle';
                                $status_text = $is_upcoming ? 'Upcoming' : 'Completed';
                                ?>
                                
                                <div class="booking-item fade-in">
                                    <div class="booking-info">
                                        <h4 style="margin-bottom: 0.25rem;"><?php echo htmlspecialchars($booking['title']); ?></h4>
                                        <p class="text-muted" style="margin-bottom: 0.5rem;">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['location']); ?>
                                        </p>
                                        <div class="booking-dates">
                                            <i class="fas fa-calendar-alt"></i>
                                            <strong>Check-in:</strong> <?php echo date('F j, Y', strtotime($booking['from_date'])); ?> | 
                                            <strong>Check-out:</strong> <?php echo date('F j, Y', strtotime($booking['to_date'])); ?>
                                        </div>
                                        <div style="margin-top: 0.5rem; display: flex; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                            <span><i class="fas fa-bed"></i> <?php echo $booking['bedrooms']; ?> Beds</span>
                                            <span><i class="fas fa-bath"></i> <?php echo $booking['bathrooms']; ?> Baths</span>
                                            <span><i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($booking['price']); ?>/month</span>
                                        </div>
                                    </div>
                                    
                                    <div style="text-align: right;">
                                        <span class="<?php echo $status_class; ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: <?php echo $is_upcoming ? 'rgba(16, 185, 129, 0.1)' : 'rgba(107, 114, 128, 0.1)'; ?>; border-radius: var(--radius-md); font-weight: 600;">
                                            <i class="<?php echo $status_icon; ?>"></i>
                                            <?php echo $status_text; ?>
                                        </span>
                                        
                                        <?php if ($is_upcoming): ?>
                                            <div style="margin-top: 1rem;">
                                                <a href="room.php?id=<?php echo $booking['room_id']; ?>" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                                    <i class="fas fa-eye"></i> View Room
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div style="margin-top: 3rem; text-align: center;">
                        <h3 style="margin-bottom: 1.5rem;">Continue Your Journey</h3>
                        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-search"></i> Find More Rooms
                            </a>
                            <a href="index.php" class="btn btn-outline">
                                <i class="fas fa-plus"></i> Make New Booking
                            </a>
                        </div>
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
        // Add animations and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to booking items
            const bookingItems = document.querySelectorAll('.booking-item');
            bookingItems.forEach(item => {
                item.addEventListener('mouseenter', () => {
                    item.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
                    item.style.transform = 'translateX(-5px)';
                    item.style.transition = 'all 0.3s ease';
                });
                
                item.addEventListener('mouseleave', () => {
                    item.style.backgroundColor = 'transparent';
                    item.style.transform = 'translateX(0)';
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

            document.querySelectorAll('.booking-item').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
