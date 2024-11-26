<?php
/**
 * Plugin Name: Bangla Archive Calendar
 * Description: Bangla Archive Calendar for date-based post filtering. Allows users to select dates using a calendar. 
 * Version: 1.0
 * Author: Sayfullah Sayeb
 * Author URI: https://github.com/SayfullahSayeb
 */
add_filter('plugin_row_meta', 'custom_plugin_row_meta', 10, 2); function custom_plugin_row_meta($links, $file) { if ($file == plugin_basename(__FILE__)) { $links[] = '<a href="https://github.com/SayfullahSayeb/Bangla-Calendar-for-WordPress" target="_blank">View Details</a>';
$links[] = 'Shortcode: <strong>[bangla_archive_calendar]</strong>'; } return $links; }


function archive_calendar_shortcode() {
    ob_start();
    ?>
    <div id="archive-calendar" class="archive-calendar" style="max-width: 100%; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <form id="archive-calendar-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <div style="display: flex; align-items: center; margin-bottom: 10px; flex-wrap: wrap;">
                <label for="year-select" style="margin-right: 5px;">বছর:</label>
                <select name="year" id="year-select" style="flex: 1; padding: 5px; margin-right: 10px; font-family: inherit;">
                    <?php
                    $current_year = date('Y');
                    for ($year = $current_year; $year >= 2000; $year--) {
                        $bangla_year = convert_to_bangla($year);
                        echo "<option value='$year' " . selected($year, $current_year, false) . ">$bangla_year</option>";
                    }
                    ?>
                </select>

                <label for="month-select" style="margin-right: 5px;">মাস:</label>
                <select name="month" id="month-select" style="flex: 1; padding: 5px; font-family: inherit;">
                    <?php
                    $current_month = date('n');
                    $months = [
                        1 => 'জানুয়ারী',
                        2 => 'ফেব্রুয়ারী',
                        3 => 'মার্চ',
                        4 => 'এপ্রিল',
                        5 => 'মে',
                        6 => 'জুন',
                        7 => 'জুলাই',
                        8 => 'অগাস্ট',
                        9 => 'সেপ্টেম্বর',
                        10 => 'অক্টোবর',
                        11 => 'নভেম্বর',
                        12 => 'ডিসেম্বর',
                    ];
                    
                    foreach ($months as $key => $month_name) {
                        echo "<option value='$key' " . selected($key, $current_month, false) . ">$month_name</option>";
                    }
                    ?>
                </select>
            </div>

            <input type="hidden" name="day" id="day-input" value="">

            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; margin-top: 10px;">
                <div style="font-weight: bold; text-align: center; font-size: 14px;">রবি</div>
                <div style="font-weight: bold; text-align: center; font-size: 14px;">সোম</div>
                <div style="font-weight: bold; text-align: center; font-size: 14px;">মঙ্গল</div>
                <div style="font-weight: bold; text-align: center; font-size: 14px;">বুধ</div>
                <div style="font-weight: bold; text-align: center; font-size: 14px;">বৃহস্পতি</div>
                <div style="font-weight: bold; text-align: center; font-size: 14px;">শুক্র</div>
                <div style="font-weight: bold; text-align: center; font-size: 14px;">শনি</div>

                <div id="calendar" style="display: contents;"></div>
            </div>

            <button type="submit" style="margin-top: 10px; background-color: #37cc33; color: white; border: none; border-radius: 5px; cursor: pointer; width: 100%;">খবর খুঁজুন</button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const yearSelect = document.getElementById('year-select');
            const monthSelect = document.getElementById('month-select');
            const dayInput = document.getElementById('day-input');

            function renderCalendar(year, month) {
                calendarEl.innerHTML = ''; // Clear previous buttons
                const daysInMonth = new Date(year, month, 0).getDate();
                const firstDay = new Date(year, month - 1, 1).getDay(); // Get the first day of the month
                const today = new Date();

                // Add empty cells for days before the first day
                for (let i = 0; i < firstDay; i++) {
                    const emptyCell = document.createElement('div');
                    calendarEl.appendChild(emptyCell);
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const dayButton = document.createElement('button');
                    dayButton.innerText = day; // Display day in English
                    dayButton.style.border = '1px solid #37cc33';
                    dayButton.style.borderRadius = '4px';
                    dayButton.style.backgroundColor = '#fff';
                    dayButton.style.color = '#000';
                    dayButton.style.cursor = 'pointer';
                    dayButton.style.width = '100%'; // Use full width for better responsiveness
                    dayButton.style.height = '25px'; // Smaller height for buttons
                    dayButton.style.fontSize = '12px'; // Smaller font size
                    dayButton.style.fontFamily = 'inherit';
                    dayButton.style.display = 'flex';
                    dayButton.style.alignItems = 'center'; // Center vertically
                    dayButton.style.justifyContent = 'center'; // Center horizontally

                    // Highlight today's date
                    if (day === today.getDate() && year == today.getFullYear() && month == today.getMonth() + 1) {
                        dayButton.style.backgroundColor = '#37cc33';
                        dayButton.style.color = 'white';
                    }

                    dayButton.onclick = () => {
                        dayInput.value = day;
                        document.querySelectorAll('#calendar button').forEach(btn => {
                            btn.style.backgroundColor = '#fff';
                            btn.style.color = '#000';
                        });
                        dayButton.style.backgroundColor = '#37cc33';
                        dayButton.style.color = 'white';
                    };

                    calendarEl.appendChild(dayButton);
                }

                // Add empty cells to fill the remaining slots (if any)
                const totalCells = 42; // 6 rows * 7 columns
                for (let i = daysInMonth + firstDay; i < totalCells; i++) {
                    const emptyCell = document.createElement('div');
                    calendarEl.appendChild(emptyCell);
                }
            }

            function updateCalendar() {
                const year = yearSelect.value;
                const month = monthSelect.value;
                renderCalendar(year, month);
            }

            monthSelect.addEventListener('change', updateCalendar);
            yearSelect.addEventListener('change', updateCalendar);

            // Initial render
            updateCalendar();
        });
    </script>
    <style>
        .archive-calendar #calendar button {
            transition: background-color 0.3s, color 0.3s;
            display: flex; /* Flex display for centering */
            align-items: center; /* Center vertically */
            justify-content: center; /* Center horizontally */
            height: 25px; /* Smaller height for buttons */
            overflow: hidden; /* Prevent overflow */
        }
        .archive-calendar #calendar {
            margin-top: 10px; /* Add spacing for better layout */
        }
    </style>
    <?php
    return ob_get_clean();
}

// Function to convert English numbers to Bangla
function convert_to_bangla($number) {
    $bangla_numerals = [
        '0' => '০',
        '1' => '১',
        '2' => '২',
        '3' => '৩',
        '4' => '৪',
        '5' => '৫',
        '6' => '৬',
        '7' => '৭',
        '8' => '৮',
        '9' => '৯',
    ];

    return strtr($number, $bangla_numerals);
}

add_shortcode('bangla_archive_calendar', 'archive_calendar_shortcode');
