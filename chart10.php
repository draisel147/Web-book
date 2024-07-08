<?php
// เรียกใช้ไฟล์ config.php เพื่อเชื่อมต่อกับฐานข้อมูล
include 'config.php';

// Process search query if submitted
if(isset($_GET['search'])) {
    $search = $_GET['search'];
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
    $age_min = isset($_GET['age_min']) ? $_GET['age_min'] : '';
    $age_max = isset($_GET['age_max']) ? $_GET['age_max'] : '';
    $book_type = isset($_GET['book_type']) ? $_GET['book_type'] : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $province = isset($_GET['province']) ? $_GET['province'] : '';
    $region = isset($_GET['region']) ? $_GET['region'] : '';
    $author = isset($_GET['author']) ? $_GET['author'] : '';
    $publisher = isset($_GET['publisher']) ? $_GET['publisher'] : '';
    $stat = isset($_GET['stat']) ? $_GET['stat'] : 'Approved';
    $price_min = isset($_GET['price_min']) ? $_GET['price_min'] : '';
    $price_max = isset($_GET['price_max']) ? $_GET['price_max'] : '';
    $time_min = isset($_GET['time_min']) ? $_GET['time_min'] : '';
    $time_max = isset($_GET['time_max']) ? $_GET['time_max'] : '';

    $sql_time = "SELECT HOUR(c.Buy_Date) AS purchase_hour,
                    COUNT(DISTINCT c.Cart_ID) AS purchase_count
                    FROM cart c
                    INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
                    INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
                    INNER JOIN book b ON od.ISBN = b.ISBN
                    INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
                    INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
                    INNER JOIN author au ON b.Auth_ID = au.Auth_ID
                    INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
                    INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
                    INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
                    INNER JOIN area a ON p.Area_ID = a.Area_ID
                    WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
                    AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";

// Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
if ($gender !== '') {
    $sql_time .= " AND bb.Gender_ID = '$gender'";
}
if ($age_min !== '' || $age_max !== '') {
 $sql_time .= " AND (";
 if ($age_min !== '') {
     $sql_time .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
 }
 if ($age_min !== '' && $age_max !== '') {
     $sql_time .= " AND ";
 }
 if ($age_max !== '') {
     $sql_time .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
 }
 $sql_time .= ")";
}

if ($book_type !== '') {
    $sql_time .= " AND b.Type_ID = '$book_type'";
}
if ($category !== '') {
    $sql_time .= " AND b.Cate_ID = '$category'";
}
if ($province !== '') {
    $sql_time .= " AND bb.Prov_ID = '$province'";
}
if ($region !== '') {
    $sql_time .= " AND p.Area_ID = '$region'";
}
if ($author !== '') {
    $sql_time .= " AND b.Auth_ID = '$author'";
}
if ($publisher !== '') {
    $sql_time .= " AND b.Publ_ID = '$publisher'";
}
if ($stat !== '') {
    $sql_time .= " AND c.Order_Status = '$stat'";
}
if ($price_min !== '' && $price_max !== '') {
 $sql_time .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
}
if ($price_min !== '' || $price_max !== '') {
 $sql_time .= " AND (";
 if ($price_min !== '') {
     $sql_time .= "b.Book_Price >= $price_min";
 }
 if ($price_min !== '' && $price_max !== '') {
     $sql_time .= " AND ";
 }
 if ($price_max !== '') {
     $sql_time .= "b.Book_Price <= $price_max";
 }
 $sql_time .= ")";
}
if (($time_min !== '' || $time_max !== '') ) {
    $sql_time .= " AND (";
    if ($time_min !== '') {
        $sql_time .= "HOUR(c.Buy_Date) >= $time_min";
    }
    if ($time_min !== '' && $time_max !== '') {
        $sql_time .= " AND ";
    }
    if ($time_max !== '') {
        $sql_time .= "HOUR(c.Buy_Date) <= $time_max";
    }
    $sql_time .= ")";
}

$sql_time .= " GROUP BY purchase_hour
            ORDER BY purchase_hour
            LIMIT 5";


    $sql_bookbad = "SELECT b.Book_Name AS book,COUNT(DISTINCT bb.BBuy_ID) AS buyer_count
                    FROM book b
                    LEFT JOIN 
                        order_detail od ON b.ISBN = od.ISBN
                    LEFT JOIN 
                        cart c ON od.Cart_ID = c.Cart_ID 
                    LEFT JOIN 
                        book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
                    LEFT JOIN 
                        book_type bt ON b.Type_ID = bt.Type_ID
                    LEFT JOIN 
                        category ct ON b.Cate_ID = ct.Cate_ID
                    LEFT JOIN 
                        author au ON b.Auth_ID = au.Auth_ID
                    LEFT JOIN 
                        publisher pu ON b.Publ_ID = pu.Publ_ID
                    LEFT JOIN 
                        gender g ON bb.Gender_ID = g.Gender_ID
                    LEFT JOIN 
                        province p ON bb.Prov_ID = p.Prov_ID
                    LEFT JOIN 
                        area a ON p.Area_ID = a.Area_ID
                    WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
                    AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";

// Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
if ($gender !== '') {
    $sql_bookbad .= " AND bb.Gender_ID = '$gender'";
}
if ($age_min !== '' || $age_max !== '') {
 $sql_bookbad .= " AND (";
 if ($age_min !== '') {
     $sql_bookbad .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
 }
 if ($age_min !== '' && $age_max !== '') {
     $sql_bookbad .= " AND ";
 }
 if ($age_max !== '') {
     $sql_bookbad .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
 }
 $sql_bookbad .= ")";
}

if ($book_type !== '') {
    $sql_bookbad .= " AND b.Type_ID = '$book_type'";
}
if ($category !== '') {
    $sql_bookbad .= " AND b.Cate_ID = '$category'";
}
if ($province !== '') {
    $sql_bookbad .= " AND bb.Prov_ID = '$province'";
}
if ($region !== '') {
    $sql_bookbad .= " AND p.Area_ID = '$region'";
}
if ($author !== '') {
    $sql_bookbad .= " AND b.Auth_ID = '$author'";
}
if ($publisher !== '') {
    $sql_bookbad .= " AND b.Publ_ID = '$publisher'";
}
if ($stat !== '') {
    $sql_bookbad .= " AND c.Order_Status = '$stat'";
}
if ($price_min !== '' && $price_max !== '') {
 $sql_bookbad .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
}
if ($price_min !== '' || $price_max !== '') {
 $sql_bookbad .= " AND (";
 if ($price_min !== '') {
     $sql_bookbad .= "b.Book_Price >= $price_min";
 }
 if ($price_min !== '' && $price_max !== '') {
     $sql_bookbad .= " AND ";
 }
 if ($price_max !== '') {
     $sql_bookbad .= "b.Book_Price <= $price_max";
 }
 $sql_bookbad .= ")";
}
if (($time_min !== '' || $time_max !== '') ) {
    $sql_bookbad .= " AND (";
    if ($time_min !== '') {
        $sql_bookbad .= "HOUR(c.Buy_Date) >= $time_min";
    }
    if ($time_min !== '' && $time_max !== '') {
        $sql_bookbad .= " AND ";
    }
    if ($time_max !== '') {
        $sql_bookbad .= "HOUR(c.Buy_Date) <= $time_max";
    }
    $sql_bookbad .= ")";
}

$sql_bookbad .= " GROUP BY book
                 ORDER BY buyer_count ASC
                 LIMIT 5";


    $sql_book = "SELECT b.Book_Name AS book,COUNT(DISTINCT bb.BBuy_ID) AS buyer_count
    FROM cart c
    INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
         INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
         INNER JOIN book b ON od.ISBN = b.ISBN
         INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
         INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
         INNER JOIN author au ON b.Auth_ID = au.Auth_ID
         INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
         INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
         INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
         INNER JOIN area a ON p.Area_ID = a.Area_ID
         WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
         AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";

// Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
if ($gender !== '') {
    $sql_book .= " AND bb.Gender_ID = '$gender'";
}
if ($age_min !== '' || $age_max !== '') {
 $sql_book .= " AND (";
 if ($age_min !== '') {
     $sql_book .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
 }
 if ($age_min !== '' && $age_max !== '') {
     $sql_book .= " AND ";
 }
 if ($age_max !== '') {
     $sql_book .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
 }
 $sql_book .= ")";
}

if ($book_type !== '') {
    $sql_book .= " AND b.Type_ID = '$book_type'";
}
if ($category !== '') {
    $sql_book .= " AND b.Cate_ID = '$category'";
}
if ($province !== '') {
    $sql_book .= " AND bb.Prov_ID = '$province'";
}
if ($region !== '') {
    $sql_book .= " AND p.Area_ID = '$region'";
}
if ($author !== '') {
    $sql_book .= " AND b.Auth_ID = '$author'";
}
if ($publisher !== '') {
    $sql_book .= " AND b.Publ_ID = '$publisher'";
}
if ($stat !== '') {
    $sql_book .= " AND c.Order_Status = '$stat'";
}
if ($price_min !== '' && $price_max !== '') {
 $sql_book .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
}
if ($price_min !== '' || $price_max !== '') {
 $sql_book .= " AND (";
 if ($price_min !== '') {
     $sql_book .= "b.Book_Price >= $price_min";
 }
 if ($price_min !== '' && $price_max !== '') {
     $sql_book .= " AND ";
 }
 if ($price_max !== '') {
     $sql_book .= "b.Book_Price <= $price_max";
 }
 $sql_book .= ")";
}
if (($time_min !== '' || $time_max !== '') ) {
    $sql_book .= " AND (";
    if ($time_min !== '') {
        $sql_book .= "HOUR(c.Buy_Date) >= $time_min";
    }
    if ($time_min !== '' && $time_max !== '') {
        $sql_book .= " AND ";
    }
    if ($time_max !== '') {
        $sql_book .= "HOUR(c.Buy_Date) <= $time_max";
    }
    $sql_book .= ")";
}

// Group by Gender_ID to aggregate results
$sql_book .= " GROUP BY b.Book_Name
                 ORDER BY buyer_count DESC
                 LIMIT 5";


    
    $sql_topspenders = "SELECT 
    bb.*,
    SUM(od.Book_Quantity * b.Book_Price) AS total_purchase
FROM 
    cart c
    INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
    INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
    INNER JOIN book b ON od.ISBN = b.ISBN
    INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
    INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
    INNER JOIN author au ON b.Auth_ID = au.Auth_ID
    INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
    INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
    INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
    INNER JOIN area a ON p.Area_ID = a.Area_ID
WHERE 
    c.Buy_Date BETWEEN '$start_date' AND '$end_date'
    AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";

// Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
if ($gender !== '') {
$sql_topspenders .= " AND bb.Gender_ID = '$gender'";
}
if ($age_min !== '' || $age_max !== '') {
$sql_topspenders .= " AND (";
if ($age_min !== '') {
$sql_topspenders .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
}
if ($age_min !== '' && $age_max !== '') {
$sql_topspenders .= " AND ";
}
if ($age_max !== '') {
$sql_topspenders .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
}
$sql_topspenders .= ")";
}

if ($book_type !== '') {
$sql_topspenders .= " AND b.Type_ID = '$book_type'";
}
if ($category !== '') {
$sql_topspenders .= " AND b.Cate_ID = '$category'";
}
if ($province !== '') {
$sql_topspenders .= " AND bb.Prov_ID = '$province'";
}
if ($region !== '') {
$sql_topspenders .= " AND p.Area_ID = '$region'";
}
if ($author !== '') {
$sql_topspenders .= " AND b.Auth_ID = '$author'";
}
if ($publisher !== '') {
$sql_topspenders .= " AND b.Publ_ID = '$publisher'";
}
if ($stat !== '') {
$sql_topspenders .= " AND c.Order_Status = '$stat'";
}
if ($price_min !== '' && $price_max !== '') {
$sql_topspenders .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
}
if ($price_min !== '' || $price_max !== '') {
$sql_topspenders .= " AND (";
if ($price_min !== '') {
$sql_topspenders .= "b.Book_Price >= $price_min";
}
if ($price_min !== '' && $price_max !== '') {
$sql_topspenders .= " AND ";
}
if ($price_max !== '') {
$sql_topspenders .= "b.Book_Price <= $price_max";
}
$sql_topspenders .= ")";
}
if (($time_min !== '' || $time_max !== '') ) {
    $sql_topspenders .= " AND (";
    if ($time_min !== '') {
        $sql_topspenders .= "HOUR(c.Buy_Date) >= $time_min";
    }
    if ($time_min !== '' && $time_max !== '') {
        $sql_topspenders .= " AND ";
    }
    if ($time_max !== '') {
        $sql_topspenders .= "HOUR(c.Buy_Date) <= $time_max";
    }
    $sql_topspenders .= ")";
}

// Group by BBuy_ID and BBuy_Name to aggregate results
$sql_topspenders .= " GROUP BY bb.BBuy_ID,bb.BBuy_Name
                        ORDER BY total_purchase DESC
                        LIMIT 5";


    // Query ข้อมูลแยกตาม cart_id
    $sql_gender = "SELECT bb.BBuy_ID, g.*, COUNT(DISTINCT bb.BBuy_ID) AS gender_count
    FROM cart c
    INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
    INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
    INNER JOIN book b ON od.ISBN = b.ISBN
    INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
    INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
    INNER JOIN author au ON b.Auth_ID = au.Auth_ID
    INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
    INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
    INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
    INNER JOIN area a ON p.Area_ID = a.Area_ID
    WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
    AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";

    // Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
    if ($gender !== '') {
        $sql_gender .= " AND bb.Gender_ID = '$gender'";
    }
    if ($age_min !== '' || $age_max !== '') {
        $sql_gender .= " AND (";
        if ($age_min !== '') {
            $sql_gender .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
        }
        if ($age_min !== '' && $age_max !== '') {
            $sql_gender .= " AND ";
        }
        if ($age_max !== '') {
            $sql_gender .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
        }
        $sql_gender .= ")";
    }
    
    if ($book_type !== '') {
        $sql_gender .= " AND b.Type_ID = '$book_type'";
    }
    if ($category !== '') {
        $sql_gender .= " AND b.Cate_ID = '$category'";
    }
    if ($province !== '') {
        $sql_gender .= " AND bb.Prov_ID = '$province'";
    }
    if ($region !== '') {
        $sql_gender .= " AND p.Area_ID = '$region'";
    }
    if ($author !== '') {
        $sql_gender .= " AND b.Auth_ID = '$author'";
    }
    if ($publisher !== '') {
        $sql_gender .= " AND b.Publ_ID = '$publisher'";
    }
    if ($stat !== '') {
        $sql_gender .= " AND c.Order_Status = '$stat'";
    }
    if ($price_min !== '' && $price_max !== '') {
        $sql_gender .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
    }
    if ($price_min !== '' || $price_max !== '') {
        $sql_gender .= " AND (";
        if ($price_min !== '') {
            $sql_gender .= "b.Book_Price >= $price_min";
        }
        if ($price_min !== '' && $price_max !== '') {
            $sql_gender .= " AND ";
        }
        if ($price_max !== '') {
            $sql_gender .= "b.Book_Price <= $price_max";
        }
        $sql_gender .= ")";
    }
    if (($time_min !== '' || $time_max !== '') ) {
        $sql_gender .= " AND (";
        if ($time_min !== '') {
            $sql_gender .= "HOUR(c.Buy_Date) >= $time_min";
        }
        if ($time_min !== '' && $time_max !== '') {
            $sql_gender .= " AND ";
        }
        if ($time_max !== '') {
            $sql_gender .= "HOUR(c.Buy_Date) <= $time_max";
        }
        $sql_gender .= ")";
    }

    // Group by Gender_ID to aggregate results
    $sql_gender .= " GROUP BY bb.Gender_ID";

    $sql_age = "SELECT 
    (YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) AS age,
    COUNT(DISTINCT bb.BBuy_ID) AS age_count
    FROM cart c
    INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
    INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
    INNER JOIN book b ON od.ISBN = b.ISBN
    INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
    INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
    INNER JOIN author au ON b.Auth_ID = au.Auth_ID
    INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
    INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
    INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
    INNER JOIN area a ON p.Area_ID = a.Area_ID
    WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
    AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";
;

// Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
if ($gender !== '') {
    $sql_age .= " AND bb.Gender_ID = '$gender'";
}
if ($age_min !== '' || $age_max !== '') {
    $sql_age .= " AND (";
    if ($age_min !== '') {
        $sql_age .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
    }
    if ($age_min !== '' && $age_max !== '') {
        $sql_age .= " AND ";
    }
    if ($age_max !== '') {
        $sql_age .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
    }
    $sql_age .= ")";
}

if ($book_type !== '') {
    $sql_age .= " AND b.Type_ID = '$book_type'";
}
if ($category !== '') {
    $sql_age .= " AND b.Cate_ID = '$category'";
}
if ($province !== '') {
    $sql_age .= " AND bb.Prov_ID = '$province'";
}
if ($region !== '') {
    $sql_age .= " AND p.Area_ID = '$region'";
}
if ($author !== '') {
    $sql_age .= " AND b.Auth_ID = '$author'";
}
if ($publisher !== '') {
    $sql_age .= " AND b.Publ_ID = '$publisher'";
}
if ($stat !== '') {
    $sql_age .= " AND c.Order_Status = '$stat'";
}
if ($price_min !== '' && $price_max !== '') {
    $sql_age .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
}
if ($price_min !== '' || $price_max !== '') {
    $sql_age .= " AND (";
    if ($price_min !== '') {
        $sql_age .= "b.Book_Price >= $price_min";
    }
    if ($price_min !== '' && $price_max !== '') {
        $sql_age .= " AND ";
    }
    if ($price_max !== '') {
        $sql_age .= "b.Book_Price <= $price_max";
    }
    $sql_age .= ")";
}
if (($time_min !== '' || $time_max !== '') ) {
    $sql_age .= " AND (";
    if ($time_min !== '') {
        $sql_age .= "HOUR(c.Buy_Date) >= $time_min";
    }
    if ($time_min !== '' && $time_max !== '') {
        $sql_age .= " AND ";
    }
    if ($time_max !== '') {
        $sql_age .= "HOUR(c.Buy_Date) <= $time_max";
    }
    $sql_age .= ")";
}

// Group by Gender_ID to aggregate results
$sql_age .= " GROUP BY bb.BBuy_Age";

    // Query ข้อมูลแยกตามวันที่
    $sql_date = "SELECT 
    DATE_FORMAT(c.Buy_Date, '%Y-%m-%d') AS purchase_date,
    SUM(CASE WHEN b.Type_ID = '1' THEN 1 ELSE 0 END) AS ebook_count,
    SUM(CASE WHEN b.Type_ID = '2' THEN 1 ELSE 0 END) AS paperback_count,
    SUM(c.Total_Price) AS total_approved_price,
    SUM(CASE WHEN b.Type_ID = '1' THEN c.Total_Price ELSE 0 END) AS total_ebook_price,
    SUM(CASE WHEN b.Type_ID = '2' THEN c.Total_Price ELSE 0 END) AS total_paperback_price,
    COUNT(*) AS purchase_count
    FROM cart c 
    INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
    INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
    INNER JOIN book b ON od.ISBN = b.ISBN
    INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
    WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
    AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";

    // Append additional conditions based on province, region, and status if needed
    if ($gender !== '') {
        $sql_date .= " AND bb.Gender_ID = '$gender'";
    }
    if ($age_min !== '' || $age_max !== '') {
        $sql_date .= " AND (";
        if ($age_min !== '') {
            $sql_date .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
        }
        if ($age_min !== '' && $age_max !== '') {
            $sql_date .= " AND ";
        }
        if ($age_max !== '') {
            $sql_date .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
        }
        $sql_date .= ")";
    }
    
    if ($book_type !== '') {
        $sql_date .= " AND b.Type_ID = '$book_type'";
    }
    if ($category !== '') {
        $sql_date .= " AND b.Cate_ID = '$category'";
    }
    if ($province !== '') {
        $sql_date .= " AND bb.Prov_ID = '$province'";
    }
    if ($region !== '') {
        $sql_date .= " AND p.Area_ID = '$region'";
    }
    if ($author !== '') {
        $sql_date .= " AND b.Auth_ID = '$author'";
    }
    if ($publisher !== '') {
        $sql_date .= " AND b.Publ_ID = '$publisher'";
    }
    if ($stat !== '') {
        $sql_date .= " AND c.Order_Status = '$stat'";
    }
    if ($price_min !== '' && $price_max !== '') {
        $sql_date .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
    }
    if ($price_min !== '' || $price_max !== '') {
        $sql_date .= " AND (";
        if ($price_min !== '') {
            $sql_date .= "b.Book_Price >= $price_min";
        }
        if ($price_min !== '' && $price_max !== '') {
            $sql_date .= " AND ";
        }
        if ($price_max !== '') {
            $sql_date .= "b.Book_Price <= $price_max";
        }
        $sql_date .= ")";
    }
    if (($time_min !== '' || $time_max !== '') ) {
        $sql_date .= " AND (";
        if ($time_min !== '') {
            $sql_date .= "HOUR(c.Buy_Date) >= $time_min";
        }
        if ($time_min !== '' && $time_max !== '') {
            $sql_date .= " AND ";
        }
        if ($time_max !== '') {
            $sql_date .= "HOUR(c.Buy_Date) <= $time_max";
        }
        $sql_date .= ")";
    }
    // Group by purchase_date to aggregate results
    $sql_date .= " GROUP BY purchase_date";

       $sql_auth = "SELECT au.Auth_Name AS author,COUNT(DISTINCT bb.BBuy_ID) AS buyer_count
       FROM cart c
       INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
            INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
            INNER JOIN book b ON od.ISBN = b.ISBN
            INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
            INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
            INNER JOIN author au ON b.Auth_ID = au.Auth_ID
            INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
            INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
            INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
            INNER JOIN area a ON p.Area_ID = a.Area_ID
            WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
            AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";
   ;
   
   // Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
   if ($gender !== '') {
       $sql_auth .= " AND bb.Gender_ID = '$gender'";
   }
   if ($age_min !== '' || $age_max !== '') {
    $sql_auth .= " AND (";
    if ($age_min !== '') {
        $sql_auth .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
    }
    if ($age_min !== '' && $age_max !== '') {
        $sql_auth .= " AND ";
    }
    if ($age_max !== '') {
        $sql_auth .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
    }
    $sql_auth .= ")";
}

   if ($book_type !== '') {
       $sql_auth .= " AND b.Type_ID = '$book_type'";
   }
   if ($category !== '') {
       $sql_auth .= " AND b.Cate_ID = '$category'";
   }
   if ($province !== '') {
       $sql_auth .= " AND bb.Prov_ID = '$province'";
   }
   if ($region !== '') {
       $sql_auth .= " AND p.Area_ID = '$region'";
   }
   if ($author !== '') {
       $sql_auth .= " AND b.Auth_ID = '$author'";
   }
   if ($publisher !== '') {
       $sql_auth .= " AND b.Publ_ID = '$publisher'";
   }
   if ($stat !== '') {
       $sql_auth .= " AND c.Order_Status = '$stat'";
   }
   if ($price_min !== '' && $price_max !== '') {
    $sql_auth .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
}
if ($price_min !== '' || $price_max !== '') {
    $sql_auth .= " AND (";
    if ($price_min !== '') {
        $sql_auth .= "b.Book_Price >= $price_min";
    }
    if ($price_min !== '' && $price_max !== '') {
        $sql_auth .= " AND ";
    }
    if ($price_max !== '') {
        $sql_auth .= "b.Book_Price <= $price_max";
    }
    $sql_auth .= ")";
}
if (($time_min !== '' || $time_max !== '') ) {
    $sql_auth .= " AND (";
    if ($time_min !== '') {
        $sql_auth .= "HOUR(c.Buy_Date) >= $time_min";
    }
    if ($time_min !== '' && $time_max !== '') {
        $sql_auth .= " AND ";
    }
    if ($time_max !== '') {
        $sql_auth .= "HOUR(c.Buy_Date) <= $time_max";
    }
    $sql_auth .= ")";
}
   
   // Group by Gender_ID to aggregate results
   $sql_auth .= " GROUP BY au.Auth_Name
                    ORDER BY buyer_count DESC
                    LIMIT 5";


   $sql_cate = "SELECT ct.Cate_Name AS book_category,COUNT(*) AS category_count
   FROM order_detail od
   INNER JOIN cart c ON od.Cart_ID = c.Cart_ID 
   INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
   INNER JOIN book b ON od.ISBN = b.ISBN
   INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
   INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
   INNER JOIN author au ON b.Auth_ID = au.Auth_ID
   INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
   INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
   INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
   INNER JOIN area a ON p.Area_ID = a.Area_ID
   WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
   AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";

    // Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
    if ($gender !== '') {
    $sql_cate .= " AND bb.Gender_ID = '$gender'";
    }
    if ($age_min !== '' || $age_max !== '') {
        $sql_cate .= " AND (";
        if ($age_min !== '') {
            $sql_cate .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
        }
        if ($age_min !== '' && $age_max !== '') {
            $sql_cate .= " AND ";
        }
        if ($age_max !== '') {
            $sql_cate .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
        }
        $sql_cate .= ")";
    }
    
    if ($book_type !== '') {
    $sql_cate .= " AND b.Type_ID = '$book_type'";
    }
    if ($category !== '') {
    $sql_cate .= " AND b.Cate_ID = '$category'";
    }
    if ($province !== '') {
    $sql_cate .= " AND bb.Prov_ID = '$province'";
    }
    if ($region !== '') {
    $sql_cate .= " AND p.Area_ID = '$region'";
    }
    if ($author !== '') {
    $sql_cate .= " AND b.Auth_ID = '$author'";
    }
    if ($publisher !== '') {
    $sql_cate .= " AND b.Publ_ID = '$publisher'";
    }
    if ($stat !== '') {
    $sql_cate .= " AND c.Order_Status = '$stat'";
    }
    if ($price_min !== '' && $price_max !== '') {
        $sql_cate .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
    }
    if ($price_min !== '' || $price_max !== '') {
        $sql_cate .= " AND (";
        if ($price_min !== '') {
            $sql_cate .= "b.Book_Price >= $price_min";
        }
        if ($price_min !== '' && $price_max !== '') {
            $sql_cate .= " AND ";
        }
        if ($price_max !== '') {
            $sql_cate .= "b.Book_Price <= $price_max";
        }
        $sql_cate .= ")";
    }
    if (($time_min !== '' || $time_max !== '') ) {
        $sql_cate .= " AND (";
        if ($time_min !== '') {
            $sql_cate .= "HOUR(c.Buy_Date) >= $time_min";
        }
        if ($time_min !== '' && $time_max !== '') {
            $sql_cate .= " AND ";
        }
        if ($time_max !== '') {
            $sql_cate .= "HOUR(c.Buy_Date) <= $time_max";
        }
        $sql_cate .= ")";
    }
// Group by Gender_ID to aggregate results
$sql_cate .= " GROUP BY ct.Cate_Name
            ORDER BY category_count DESC
            LIMIT 5";


$sql_publ = "SELECT pu.Publ_Name AS publisher,COUNT(*) AS publisher_count
            FROM order_detail od
            INNER JOIN cart c ON od.Cart_ID = c.Cart_ID 
            INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
            INNER JOIN book b ON od.ISBN = b.ISBN
            INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
            INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
            INNER JOIN author au ON b.Auth_ID = au.Auth_ID
            INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
            INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
            INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
            INNER JOIN area a ON p.Area_ID = a.Area_ID
            WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
            AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";

    // Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
        if ($gender !== '') {
        $sql_publ .= " AND bb.Gender_ID = '$gender'";
        }
        if ($age_min !== '' || $age_max !== '') {
            $sql_publ .= " AND (";
            if ($age_min !== '') {
                $sql_publ .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
            }
            if ($age_min !== '' && $age_max !== '') {
                $sql_publ .= " AND ";
            }
            if ($age_max !== '') {
                $sql_publ .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
            }
            $sql_publ .= ")";
        }
        
        if ($book_type !== '') {
        $sql_publ .= " AND b.Type_ID = '$book_type'";
        }
        if ($category !== '') {
        $sql_publ .= " AND b.Cate_ID = '$category'";
        }
        if ($province !== '') {
        $sql_publ .= " AND bb.Prov_ID = '$province'";
        }
        if ($region !== '') {
        $sql_publ .= " AND p.Area_ID = '$region'";
        }
        if ($author !== '') {
        $sql_publ .= " AND b.Auth_ID = '$author'";
        }
        if ($publisher !== '') {
        $sql_publ .= " AND b.Publ_ID = '$publisher'";
        }
        if ($stat !== '') {
        $sql_publ .= " AND c.Order_Status = '$stat'";
        }
        if ($price_min !== '' && $price_max !== '') {
            $sql_publ .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
        }
        if ($price_min !== '' || $price_max !== '') {
            $sql_publ .= " AND (";
            if ($price_min !== '') {
                $sql_publ .= "b.Book_Price >= $price_min";
            }
            if ($price_min !== '' && $price_max !== '') {
                $sql_publ .= " AND ";
            }
            if ($price_max !== '') {
                $sql_publ .= "b.Book_Price <= $price_max";
            }
            $sql_publ .= ")";
        }
        if (($time_min !== '' || $time_max !== '') ) {
            $sql_publ .= " AND (";
            if ($time_min !== '') {
                $sql_publ .= "HOUR(c.Buy_Date) >= $time_min";
            }
            if ($time_min !== '' && $time_max !== '') {
                $sql_publ .= " AND ";
            }
            if ($time_max !== '') {
                $sql_publ .= "HOUR(c.Buy_Date) <= $time_max";
            }
            $sql_publ .= ")";
        }
// Group by Gender_ID to aggregate results
$sql_publ .= " GROUP BY pu.Publ_Name
                ORDER BY publisher_count DESC
                LIMIT 5";


$sql_prov = "SELECT bb.BBuy_ID, p.*, COUNT(DISTINCT bb.BBuy_ID) AS province_count
            FROM cart c
            INNER JOIN order_detail od ON c.Cart_ID = od.Cart_ID 
            INNER JOIN book_buyer bb ON c.BBuy_ID = bb.BBuy_ID 
            INNER JOIN book b ON od.ISBN = b.ISBN
            INNER JOIN book_type bt ON b.Type_ID = bt.Type_ID
            INNER JOIN category ct ON b.Cate_ID = ct.Cate_ID
            INNER JOIN author au ON b.Auth_ID = au.Auth_ID
            INNER JOIN publisher pu ON b.Publ_ID = pu.Publ_ID
            INNER JOIN gender g ON bb.Gender_ID = g.Gender_ID
            INNER JOIN province p ON bb.Prov_ID = p.Prov_ID
            INNER JOIN area a ON p.Area_ID = a.Area_ID
            WHERE c.Buy_Date BETWEEN '$start_date' AND '$end_date'
            AND (b.Book_Name LIKE '%$search%' OR bb.BBuy_Name LIKE '%$search%')";

// Append additional conditions based on gender, age range, book type, category, province, region, author, publisher, and status
        if ($gender !== '') {
        $sql_prov .= " AND bb.Gender_ID = '$gender'";
        }
        if ($age_min !== '' || $age_max !== '') {
            $sql_prov .= " AND (";
            if ($age_min !== '') {
                $sql_prov .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) >= $age_min";
            }
            if ($age_min !== '' && $age_max !== '') {
                $sql_prov .= " AND ";
            }
            if ($age_max !== '') {
                $sql_prov .= "(YEAR(CURRENT_DATE) - YEAR(bb.BBuy_BirthDate)) <= $age_max";
            }
            $sql_prov .= ")";
        }
        
        if ($book_type !== '') {
        $sql_prov .= " AND b.Type_ID = '$book_type'";
        }
        if ($category !== '') {
        $sql_prov .= " AND b.Cate_ID = '$category'";
        }
        if ($province !== '') {
        $sql_prov .= " AND bb.Prov_ID = '$province'";
        }
        if ($region !== '') {
        $sql_prov .= " AND p.Area_ID = '$region'";
        }
        if ($author !== '') {
        $sql_prov .= " AND b.Auth_ID = '$author'";
        }
        if ($publisher !== '') {
        $sql_prov .= " AND b.Publ_ID = '$publisher'";
        }
        if ($stat !== '') {
        $sql_prov .= " AND c.Order_Status = '$stat'";
        }
        if ($price_min !== '' && $price_max !== '') {
            $sql_prov .= " AND b.Book_Price BETWEEN $price_min AND $price_max";
        }
        if ($price_min !== '' || $price_max !== '') {
            $sql_prov .= " AND (";
            if ($price_min !== '') {
                $sql_prov .= "b.Book_Price >= $price_min";
            }
            if ($price_min !== '' && $price_max !== '') {
                $sql_prov .= " AND ";
            }
            if ($price_max !== '') {
                $sql_prov .= "b.Book_Price <= $price_max";
            }
            $sql_prov .= ")";
        }
        if (($time_min !== '' || $time_max !== '') ) {
            $sql_prov .= " AND (";
            if ($time_min !== '') {
                $sql_prov .= "HOUR(c.Buy_Date) >= $time_min";
            }
            if ($time_min !== '' && $time_max !== '') {
                $sql_prov .= " AND ";
            }
            if ($time_max !== '') {
                $sql_prov .= "HOUR(c.Buy_Date) <= $time_max";
            }
            $sql_prov .= ")";
        }

// Group by Gender_ID to aggregate results
$sql_prov .= " GROUP BY pu.Publ_Name;";

} else {
    // กรณีที่ไม่มีการส่งคำค้นหา
    echo "กรุณากรอกคำค้นหา";
} 
$resultage = $conn->query($sql_age);
$resultgender = $conn->query($sql_gender);
$resultAuthors = $conn->query($sql_auth);
$resultCategories = $conn->query($sql_cate);
$resultPublishers = $conn->query($sql_publ);
$resultBuyersByAddress = $conn->query($sql_prov);
$resultdate = $conn->query($sql_date);
$resultdate2 = $conn->query($sql_date);
$resulttopspend = $conn->query($sql_topspenders);
$resultBook = $conn->query($sql_book);
$resultBookBad = $conn->query($sql_bookbad);
$resultTime = $conn->query($sql_time);

$timeData = array();
while ($rowTime = $resultTime->fetch_assoc()){
    $timeData[$rowTime['purchase_hour']] = $rowTime['purchase_count'];
}


$bookbadData = array();
while ($rowBookBad = $resultBookBad->fetch_assoc()) {
    $bookbadData[$rowBookBad['book']] = $rowBookBad['buyer_count'];
}


$bookData = array();
while ($rowBook = $resultBook->fetch_assoc()) {
    $bookData[$rowBook['book']] = $rowBook['buyer_count'];
}

$topspenddata = array();
while ($row = $resulttopspend->fetch_assoc()) {
    $topspenddata[$row['BBuy_Name']] = $row['total_purchase'];
}

$datedata = array();
while ($row = $resultdate->fetch_assoc()) {
    $datedata['purchase_dates'][] = $row['purchase_date'];
    $datedata['total_approved_prices'][] = $row['total_approved_price'];
    $datedata['total_ebook_prices'][] = $row['total_ebook_price'];
    $datedata['total_paperback_prices'][] = $row['total_paperback_price'];
}

$datedata2 = array();
while ($row = $resultdate2->fetch_assoc()) {
    $datedata2['purchase_dates'][] = $row['purchase_date'];
    $datedata2['ebook_counts'][] = $row['ebook_count'];
    $datedata2['paperback_counts'][] = $row['paperback_count'];
    $datedata2['purchase_count'][] = $row['purchase_count'];
}

// Initialize an array to store purchase date data for the chart

// Store the age and the count of book buyers for each age
$ageData = array();
while ($row = $resultage->fetch_assoc()) {
    $ageData[$row['age']] = $row['age_count'];
}

// Initialize an array to store gender data for the chart
$genderData = array();
while ($rowGender = $resultgender->fetch_assoc()) {
    $genderData[$rowGender['Gender_Name']] = $rowGender['gender_count'];
}
// Initialize an array to store data for the chart
$authorData = array();
while ($rowAuthors = $resultAuthors->fetch_assoc()) {
    $authorData[$rowAuthors['author']] = $rowAuthors['buyer_count'];
}

// Initialize an array to store data for the chart
$categoryData = array();
while ($rowCategories = $resultCategories->fetch_assoc()) {
    $categoryData[$rowCategories['book_category']] = $rowCategories['category_count'];
}

// Initialize an array to store data for the chart
$publisherData = array();
while ($rowPublishers = $resultPublishers->fetch_assoc()) {
    $publisherData[$rowPublishers['publisher']] = $rowPublishers['publisher_count'];
}

// Initialize an array to store data for the chart
$addressData = array();
while ($rowAddress = $resultBuyersByAddress->fetch_assoc()) {
    $addressData[$rowAddress['Prov_Name']] = $rowAddress['province_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Buyers Analysis</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 4columns */
            grid-template-rows: repeat(4, auto); /* 3 rows */
            gap: 20px;
            padding: 20px;
        }

        .chart-container {
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); /* Add shadow effect to the chart */
        }
    </style>
</head>
<body>
<div class="container">
        <div class="chart-container">
            <canvas id="purchaseTimeChart" width="350" height="300"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="purchaseTimeChart2" width="350" height="300"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="genderPieChart" width="200" height="200"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="topAuthorsPieChart" width="300" height="300"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="topCategoriesPieChart" width="200" height="200"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="topPublishersPieChart" width="200" height="200"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="buyerAgePieChart" width="300" height="300"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="buyersByAddressPieChart" width="200" height="200"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="topsale" width="200" height="200"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="topspender" width="300" height="300"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="bookbad" width="200" height="200"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="toptime" width="200" height="200"></canvas>
        </div>

    </div>

    <script>
        // Gender data
        const genderData = <?php echo json_encode($genderData); ?>;
        const genderLabels = Object.keys(genderData);
        const genderValues = Object.values(genderData);
        const genderBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];

        // Gender Chart Configuration
        const genderConfig = {
            type: 'bar',
            data: {
                labels: genderLabels,
                datasets: [{
                    label: 'Gender Distribution',
                    data: genderValues,
                    backgroundColor: genderBackgroundColors,
                    borderWidth: 1
                }
            ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Gender Distribution of Book Buyers'
                    },
                    legend: {
                        display: false,
                    }
                }

            }
        };

        // Top Authors data
        const authorData = <?php echo json_encode($authorData); ?>;
        const authorLabels = Object.keys(authorData);
        const authorValues = Object.values(authorData);
        const authorBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];

        // Top Authors Chart Configuration
        const authorConfig = {
            type: 'pie',
            data: {
                labels: authorLabels,
                datasets: [{
                    label: 'Orders by Top Authors',
                    data: authorValues,
                    backgroundColor: authorBackgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top Authors by Orders'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        };

        // Top Categories data
        const categoryData = <?php echo json_encode($categoryData); ?>;
        const categoryLabels = Object.keys(categoryData);
        const categoryValues = Object.values(categoryData);
        const categoryBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];

        // Top Categories Chart Configuration
        const categoryConfig = {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Orders by Top Categories',
                    data: categoryValues,
                    backgroundColor: categoryBackgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top Categories by Orders'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        };

        // Top Publishers data
        const publisherData = <?php echo json_encode($publisherData); ?>;
        const publisherLabels = Object.keys(publisherData);
        const publisherValues = Object.values(publisherData);
        const publisherBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];

        // Top Publishers Chart Configuration
        const publisherConfig = {
            type: 'pie',
            data: {
                labels: publisherLabels,
                datasets: [{
                    label: 'Orders by Top Publishers',
                    data: publisherValues,
                    backgroundColor: publisherBackgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top Publishers by Orders'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        };

        // Buyer Age data
        const ageData = <?php echo json_encode($ageData); ?>;
        const ageLabels = Object.keys(ageData);
        const ageValues = Object.values(ageData);
        const ageBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];

        // Buyer Age Chart Configuration
        const ageConfig = {
            type: 'pie',
            data: {
                labels: ageLabels,
                datasets: [{
                    label: 'Book Buyers by Age Group',
                    data: ageValues,
                    backgroundColor: ageBackgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Book Buyers by Age Group'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        };

        // Buyers by Address data
        const addressData = <?php echo json_encode($addressData); ?>;
        const addressLabels = Object.keys(addressData);
        const addressValues = Object.values(addressData);
        const addressBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];

        // Buyers by Address Chart Configuration
        const addressConfig = {
            type: 'pie',
            data: {
                labels: addressLabels,
                datasets: [{
                    label: 'Buyers by Address',
                    data: addressValues,
                    backgroundColor: addressBackgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Buyers by Address'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        };
        // ข้อมูลการซื้อของแต่ละวัน
        const purchaseData = <?php echo json_encode($datedata); ?>;

        // กำหนดการสร้างแผนภูมิเส้น
        const datetotalconfig = {
            type: 'line',
            data: {
                labels: purchaseData.purchase_dates,
                datasets: [
                    {
                        label: 'Total Approved Price',
                        data: purchaseData.total_approved_prices,
                        fill: false,
                        borderColor: 'rgb(54, 162, 235)',
                        tension: 0.1
                    },
                    {
                        label: 'Total eBook Price',
                        data: purchaseData.total_ebook_prices,
                        fill: false,
                        borderColor: 'rgb(255, 206, 86)',
                        tension: 0.1
                    },
                    {
                        label: 'Total Paperback Price',
                        data: purchaseData.total_paperback_prices,
                        fill: false,
                        borderColor: 'rgb(153, 102, 255)',
                        tension: 0.1
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Daily Purchases'
                    },
                    legend: {
                        display: false,
                    }
                }
            },
        };


        // ข้อมูลการซื้อของแต่ละวัน
        const purchaseData2 = <?php echo json_encode($datedata2); ?>;

        // กำหนดการสร้างแผนภูมิเส้น
        const dateconfig = {
            type: 'line',
            data: {
                labels: purchaseData2.purchase_dates,
                datasets: [{
                        label: 'eBook Count',
                        data: purchaseData2.ebook_counts,
                        fill: false,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    },
                    {
                        label: 'Paperback Count',
                        data: purchaseData2.paperback_counts,
                        fill: false,
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1
                    },
                    {
                        label: 'Total Paperback Price',
                        data: purchaseData2.purchase_count,
                        fill: false,
                        borderColor: 'rgb(153, 102, 255)',
                        tension: 0.1
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Daily Purchases'
                    },
                    legend: {
                        display: false,
                    }
                }
            },
        };


        // Top spend data
        const topspendData = <?php echo json_encode($topspenddata); ?>;
        const topspendLabels = Object.keys(topspendData);
        const topspendValues = Object.values(topspendData);
        const topspendBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];

        // Top spend Chart Configuration
        const topspendConfig = {
            type: 'pie',
            data: {
                labels: topspendLabels,
                datasets: [{
                    label: 'Orders by Top Spenders',
                    data: topspendValues,
                    backgroundColor: topspendBackgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top Spenders by Orders'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        };


        // Top Books data
        const bookData = <?php echo json_encode($bookData); ?>;
        const bookLabels = Object.keys(bookData);
        const bookValues = Object.values(bookData);
        const bookBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];

                // Top Books Chart Configuration
                const bookConfig = {
            type: 'pie',
            data: {
                labels: bookLabels,
                datasets: [{
                    label: 'Orders by Top Books',
                    data: bookValues,
                    backgroundColor: bookBackgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top Books by Orders'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        };


// Less Book data
const bookbadData = <?php echo json_encode($bookbadData); ?>;
const bookbadLabels = Object.keys(bookbadData);
const bookbadValues = Object.values(bookbadData);
const bookbadBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];

// Check if there is no data available
if (bookbadValues.every(count => count === 0)) {
    // No data available, create a chart to display this information
    const noDataConfig = {
        type: 'pie',
        data: {
            labels: ['No data available'],
            datasets: [{
                data: [1], // To create a line in the chart
                backgroundColor: ['#dddddd'], // Gray color
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'No Data Available'
                },
                legend: {
                    display: false
                }
            }
        }
    };

    // Create a chart with no data available
    var noDataChart = new Chart(
        document.getElementById('bookbad'),
        noDataConfig
    );
} else {
    // Data available, create the chart as usual
    const bookbadConfig = {
        type: 'pie',
        data: {
            labels: bookbadLabels,
            datasets: [{
                label: 'Orders by Less Buy Books',
                data: bookbadValues,
                backgroundColor: bookbadBackgroundColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Less Books by Orders'
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    };
      
    // Create less Books Chart
    var bookbadChart = new Chart(
        document.getElementById('bookbad'),
        bookbadConfig
    );
}

            // Top Times data
            const timeData = <?php echo json_encode($timeData); ?>;
            const timeLabels = [];
            const timeValues = [];
            const timeBackgroundColors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9966'];
            for (let i = 0; i < 24; i++) {
                const hour = (i < 10) ? '0' + i : i.toString(); 
                timeLabels.push(hour + ':00');
                // ถ้ามีข้อมูลในแต่ละชั่วโมง ให้ใส่ข้อมูลจำนวนการสั่งซื้อลงใน datasets
                if (timeData.hasOwnProperty(i)) {
                    timeValues.push(timeData[i]);
                } else {
                    timeValues.push(0); // ถ้าไม่มีข้อมูลในชั่วโมงนั้น ให้ใส่ค่า 0 เข้าไป
                }
            }

                // Top Books Chart Configuration
                const timeConfig = {
            type: 'line',
            data: {
                labels: timeLabels,
                datasets: [{
                    label: 'Orders by Top Buy Times',
                    data: timeValues,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)', // เปลี่ยนสีพื้นหลัง
                    borderColor: 'rgba(255, 99, 132, 1)', // เปลี่ยนสีเส้นกราฟ
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Top Buy Times by Orders'
                    },
                    legend: {
                        display: false,
                    }
                }
            }
        };
            // Create Top Books Chart
            var timeChart = new Chart(
            document.getElementById('toptime'),
            timeConfig
        );

            // Create Top Books Chart
            var bookChart = new Chart(
            document.getElementById('topsale'),
            bookConfig
        );

        // Create Top Spender Chart
        var topspendChart = new Chart(
            document.getElementById('topspender'),
            topspendConfig
        );



        // สร้างกราฟเส้นและแสดงผลบน canvas
        var countChart = new Chart(
            document.getElementById('purchaseTimeChart2'),
            dateconfig
        );




        // สร้างกราฟเส้นและแสดงผลบน canvas
        var totalChart = new Chart(
            document.getElementById('purchaseTimeChart'),
            datetotalconfig
        );
        

        // Create Gender Chart
        var genderChart = new Chart(
            document.getElementById('genderPieChart'),
            genderConfig
        );

        // Create Top Authors Chart
        var authorChart = new Chart(
            document.getElementById('topAuthorsPieChart'),
            authorConfig
        );

        // Create Top Categories Chart
        var categoryChart = new Chart(
            document.getElementById('topCategoriesPieChart'),
            categoryConfig
        );

        // Create Top Publishers Chart
        var publisherChart = new Chart(
            document.getElementById('topPublishersPieChart'),
            publisherConfig
        );

        // Create Buyer Age Chart
        var ageChart = new Chart(
            document.getElementById('buyerAgePieChart'),
            ageConfig
        );

        // Create Buyers by Address Chart
        var addressChart = new Chart(
            document.getElementById('buyersByAddressPieChart'),
            addressConfig
        );
    </script>
</body>
</html>