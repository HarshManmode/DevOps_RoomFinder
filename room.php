<?php
include "config.php";

$id = intval($_GET['id']);
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$q = mysqli_query($conn, "SELECT * FROM rooms WHERE id=$id");
$room = mysqli_fetch_assoc($q);

if (!$room) {
    header("Location: index.php");
    exit();
}

// Handle booking form submission
$message = '';
if ($_POST && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $room_id = $room['id'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    
    // Validate dates
    if (strtotime($from_date) >= strtotime($to_date)) {
        $message = "Error: Check-out date must be after check-in date.";
    } else {
        // Check for existing bookings
        $check_booking = mysqli_query($conn, "SELECT * FROM bookings WHERE room_id=$room_id AND 
            ((from_date <= '$from_date' AND to_date > '$from_date') OR 
             (from_date < '$to_date' AND to_date >= '$to_date') OR
             (from_date >= '$from_date' AND to_date <= '$to_date'))");
        
        if (mysqli_num_rows($check_booking) > 0) {
            $message = "This room is already booked for the selected dates. Please choose different dates.";
        } else {
            // Insert booking
            $insert_query = "INSERT INTO bookings(user_id, room_id, from_date, to_date) 
                           VALUES($user_id, $room_id, '$from_date', '$to_date')";
            
            if (mysqli_query($conn, $insert_query)) {
                $message = "success";
            } else {
                $message = "Booking failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room['title']); ?> - PG Rental</title>
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
            <?php if ($message === 'success'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Booking confirmed! Check your <a href="my_bookings.php" class="text-primary">bookings</a> for details.
                </div>
            <?php elseif ($message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="room-details fade-in">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">
                    <div>
                        <div style="width: 100%; height: 300px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <i class="fas fa-bed fa-5x" style="color: white; opacity: 0.8;"></i>
                        </div>
                        <div style="display: flex; gap: 1rem; justify-content: center;">
                            <div style="background: rgba(59, 130, 246, 0.1); padding: 0.5rem 1rem; border-radius: var(--radius-md); border-left: 4px solid var(--primary-color);">
                                <i class="fas fa-bed"></i> <?php echo htmlspecialchars($room['bedrooms']); ?> Bedrooms
                            </div>
                            <div style="background: rgba(16, 185, 129, 0.1); padding: 0.5rem 1rem; border-radius: var(--radius-md); border-left: 4px solid var(--accent-color);">
                                <i class="fas fa-bath"></i> <?php echo htmlspecialchars($room['bathrooms']); ?> Bathrooms
                            </div>
                        </div>
                    </div>

                    <div>
                        <h1 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($room['title']); ?></h1>
                        <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                            <span class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($room['location']); ?>
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-ruler-combined"></i>
                                <?php echo htmlspecialchars($room['size']); ?> sq ft
                            </span>
                        </div>
                        
                        <div class="room-price" style="font-size: 2rem; margin-bottom: 2rem;">
                            ₹<?php echo number_format($room['price']); ?> <span class="text-muted">/ month</span>
                        </div>

                        <div style="margin-bottom: 2rem;">
                            <h3 style="margin-bottom: 1rem;">Description</h3>
                            <p style="line-height: 1.8; color: var(--text-secondary);"><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                        </div>

                        <div class="room-features">
                            <div class="feature-item">
                                <h4><i class="fas fa-wifi"></i> Internet</h4>
                                <p class="text-muted">High-speed Wi-Fi included</p>
                            </div>
                            <div class="feature-item">
                                <h4><i class="fas fa-utensils"></i> Kitchen</h4>
                                <p class="text-muted">Fully equipped kitchen</p>
                            </div>
                            <div class="feature-item">
                                <h4><i class="fas fa-couch"></i> Furnished</h4>
                                <p class="text-muted">Fully furnished with modern furniture</p>
                            </div>
                            <div class="feature-item">
                                <h4><i class="fas fa-shield-alt"></i> Security</h4>
                                <p class="text-muted">24/7 security and CCTV</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Book This Room</h2>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="form-container" style="max-width: 600px;">
                        <form method="post" action="room.php?id=<?php echo $room['id']; ?>">
                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="from_date">
                                        <i class="fas fa-calendar-alt"></i> Check-in Date
                                    </label>
                                    <input type="date" id="from_date" name="from_date" class="form-input" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="to_date">
                                        <i class="fas fa-calendar-check"></i> Check-out Date
                                    </label>
                                    <input type="date" id="to_date" name="to_date" class="form-input" required>
                                </div>
                            </div>

                            <div style="background: rgba(59, 130, 246, 0.1); padding: 1.5rem; border-radius: var(--radius-md); border-left: 4px solid var(--primary-color); margin: 1.5rem 0;">
                                <h4 style="margin-bottom: 0.5rem;"><i class="fas fa-info-circle"></i> Booking Information</h4>
                                <p class="text-muted" style="margin-bottom: 1rem;">Please select your preferred dates to proceed with booking.</p>
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                    <span style="background: white; padding: 0.5rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                        <i class="fas fa-rupee-sign"></i> Monthly Rate: ₹<?php echo number_format($room['price']); ?>
                                    </span>
                                    <span style="background: white; padding: 0.5rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                        <i class="fas fa-bed"></i> <?php echo htmlspecialchars($room['bedrooms']); ?> Bedrooms
                                    </span>
                                    <span style="background: white; padding: 0.5rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                        <i class="fas fa-bath"></i> <?php echo htmlspecialchars($room['bathrooms']); ?> Bathrooms
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary w-full">
                                    <i class="fas fa-calendar-plus"></i> Book Now
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Please <a href="login.php" class="text-primary">login</a> or <a href="register.php" class="text-primary">register</a> to book this room.
                    </div>
                    
                    <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="register.php" class="btn btn-outline">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div style="margin-top: 3rem; text-align: center;">
                <a href="index.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Rooms
                </a>
            </div>
        </div>
    </main>

    <footer style="margin-top: auto; padding: 2rem 0; border-top: 1px solid var(--border-color); color: var(--text-secondary);">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> PG Rental. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Add form validation and interactions
        document.addEventListener('DOMContentLoaded', function() {
            const fromDate = document.getElementById('from_date');
            const toDate = document.getElementById('to_date');
            
            if (fromDate && toDate) {
                const today = new Date();
                const tomorrow = new Date(today);
                tomorrow.setDate(tomorrow.getDate() + 1);
                
                // Set minimum date to tomorrow
                const minDate = tomorrow.toISOString().split('T')[0];
                fromDate.min = minDate;
                toDate.min = minDate;

                // Update to_date minimum when from_date changes
                fromDate.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    const nextDay = new Date(selectedDate);
                    nextDay.setDate(nextDay.getDate() + 1);
                    toDate.min = nextDay.toISOString().split('T')[0];
                    
                    // If to_date is before the new minimum, clear it
                    if (toDate.value && new Date(toDate.value) <= selectedDate) {
                        toDate.value = '';
                    }
                });
            }

            // Add hover effects to feature items
            const featureItems = document.querySelectorAll('.feature-item');
            featureItems.forEach(item => {
                item.addEventListener('mouseenter', () => {
                    item.style.transform = 'translateY(-2px)';
                    item.style.transition = 'transform 0.3s ease';
                });
                
                item.addEventListener('mouseleave', () => {
                    item.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>
