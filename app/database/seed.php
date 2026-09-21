<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This seeder can only be run from the command line.\n");
}

require_once dirname(__DIR__, 2) . '/path.php';
require_once __DIR__ . '/connect.php';

$force = in_array('--force', $argv, true);

function seed_info(string $message): void
{
    fwrite(STDOUT, $message . PHP_EOL);
}

function seed_placeholder_images(string $dir, bool $overwrite): void
{
    if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
        fwrite(STDERR, "Cannot create upload directory: {$dir}\n");
        return;
    }
    @chmod($dir, 0777);

    $images = [
        'slide_1.jpg' => [33, 150, 243],
        'slide_2.jpg' => [76, 175, 80],
        'slide_3.jpg' => [255, 152, 0],
        'post_php.jpg' => [156, 39, 176],
        'post_js.jpg' => [255, 193, 7],
        'post_css.jpg' => [3, 169, 244],
        'post_html.jpg' => [244, 67, 54],
        'post_frontend.jpg' => [0, 150, 136],
        'post_wp.jpg' => [63, 81, 181],
        'post_node.jpg' => [139, 195, 74],
    ];

    foreach ($images as $name => $rgb) {
        $path = $dir . DIRECTORY_SEPARATOR . $name;
        if (is_file($path) && !$overwrite) {
            continue;
        }
        $im = imagecreatetruecolor(1280, 640);
        $bg = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
        $fg = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, 0, 0, 1280, 640, $bg);
        imagestring($im, 5, 48, 300, 'My blog: ' . $name, $fg);
        imagejpeg($im, $path, 85);
        imagedestroy($im);
    }
}

function seed_demo_data(PDO $pdo): void
{
    $adminUser = env_value('ADMIN_USERNAME', 'admin');
    $adminEmail = env_value('ADMIN_EMAIL', 'admin@myblog.local');
    $adminPass = password_hash((string) env_value('ADMIN_PASSWORD', 'admin123'), PASSWORD_DEFAULT);
    $demoUser = env_value('DEMO_USERNAME', 'demo');
    $demoEmail = env_value('DEMO_EMAIL', 'demo@myblog.local');
    $demoPass = password_hash((string) env_value('DEMO_PASSWORD', 'user123'), PASSWORD_DEFAULT);

    $insertUser = $pdo->prepare(
        'INSERT INTO users (id, admin, username, email, password) VALUES (:id, :admin, :username, :email, :password)'
    );
    $insertUser->execute([
        'id' => 1,
        'admin' => 1,
        'username' => $adminUser,
        'email' => $adminEmail,
        'password' => $adminPass,
    ]);
    $insertUser->execute([
        'id' => 2,
        'admin' => 0,
        'username' => $demoUser,
        'email' => $demoEmail,
        'password' => $demoPass,
    ]);

    $topics = [
        [1, 'JavaScript', 'Notes and walkthroughs of JavaScript for the blog.'],
        [2, 'PHP', 'Server-side development with PHP.'],
        [3, 'HTML', 'Markup, semantics, and accessibility.'],
        [4, 'CSS', 'Styles, layout, and responsive design.'],
        [5, 'Frontend', 'The client side of web applications.'],
        [6, 'WordPress', 'Sites and themes built with WordPress.'],
        [7, 'Node.js', 'JavaScript on the server.'],
        [8, 'C++', 'Carousel featured posts (category id=8, as in the original code).'],
    ];
    $insertTopic = $pdo->prepare(
        'INSERT INTO topics (id, name, description) VALUES (:id, :name, :description)'
    );
    foreach ($topics as [$id, $name, $description]) {
        $insertTopic->execute(['id' => $id, 'name' => $name, 'description' => $description]);
    }

    $posts = [
        [
            'id' => 1,
            'id_user' => 1,
            'title' => 'How to spin up a dynamic site with PHP and MySQL',
            'img' => 'slide_1.jpg',
            'content' => "A classic learning stack: PHP renders HTML, MySQL stores users and posts, and templates assemble the home page, categories, and a single post view.\n\nYou do not need to install OpenServer by hand — the project runs in Docker. After signing in as admin you can add articles, upload images, and moderate comments.",
            'status' => 1,
            'id_topic' => 8,
        ],
        [
            'id' => 2,
            'id_user' => 1,
            'title' => 'Docker for a PHP lab: the database next to the code',
            'img' => 'slide_2.jpg',
            'content' => "A PHP-Apache container serves the same frontend. MySQL runs beside it and loads the schema from docker/mysql/schema.sql.\n\nLinks on the site use APP_URL. If you change the port in .env, update APP_URL and restart Compose.",
            'status' => 1,
            'id_topic' => 8,
        ],
        [
            'id' => 3,
            'id_user' => 1,
            'title' => 'Featured post carousel and the home feed',
            'img' => 'slide_3.jpg',
            'content' => "The home page has two blocks. The carousel takes posts from category id=8 (that is how the original code worked). The feed shows published posts with status=1, two per page.\n\nDrafts stay in the admin panel and never appear on the home page until you publish them.",
            'status' => 1,
            'id_topic' => 8,
        ],
        [
            'id' => 4,
            'id_user' => 1,
            'title' => 'PHP and PDO: connecting through environment variables',
            'img' => 'post_php.jpg',
            'content' => "connect.php no longer stores localhost and a password in plain sight. Host, database name, user, and charset come from .env.\n\nIn Docker, DB_HOST must be db — that is the service name in docker-compose.yml. From the host machine, MySQL is available on MYSQL_HOST_PORT (3307 by default).",
            'status' => 1,
            'id_topic' => 2,
        ],
        [
            'id' => 5,
            'id_user' => 2,
            'title' => 'JavaScript on blog pages: Bootstrap and the carousel',
            'img' => 'post_js.jpg',
            'content' => "The frontend is unchanged: Bootstrap 5 from a CDN, custom CSS, and scripts.js. The home carousel is powered by Bootstrap, so no extra framework is required.\n\nIf you add a new script, put it in assets/js — the container mounts the project directly, and a refresh is enough to see the change.",
            'status' => 1,
            'id_topic' => 1,
        ],
        [
            'id' => 6,
            'id_user' => 2,
            'title' => 'Blog CSS: header, post cards, and footer',
            'img' => 'post_css.jpg',
            'content' => "Main styles live in assets/css/style1.css, with a separate admin.css for the dashboard. The grey header, preview cards, and contact footer all come from the original layout.\n\nTo change colors, edit the CSS locally. The Docker volume picks up the file without rebuilding the image.",
            'status' => 1,
            'id_topic' => 4,
        ],
        [
            'id' => 7,
            'id_user' => 1,
            'title' => 'HTML semantics: headings, articles, and comment forms',
            'img' => 'post_html.jpg',
            'content' => "The post page (single.php) shows the title, author, date, full text, and a comment form. Comments with status=1 appear under the post at once; the rest wait for moderation in the admin panel.\n\nThe form asks for an email and at least 25 characters of text — that rule lives in commentaries.php.",
            'status' => 1,
            'id_topic' => 3,
        ],
        [
            'id' => 8,
            'id_user' => 2,
            'title' => 'Frontend with no build step: the same template, new data from MySQL',
            'img' => 'post_frontend.jpg',
            'content' => "There is no React and no npm build. PHP injects posts, categories, and comments into ready-made templates. That is convenient for a lab: you look at HTML, see SQL, and understand the query.\n\nSidebar categories go to category.php?id=..., and search matches title and content.",
            'status' => 1,
            'id_topic' => 5,
        ],
        [
            'id' => 9,
            'id_user' => 1,
            'title' => 'Not WordPress, but the theme logic feels familiar',
            'img' => 'post_wp.jpg',
            'content' => "This blog is not WordPress, yet the ideas are the same: posts, categories, comments, and an admin role. The difference is that everything is written in educational PHP without a CMS.\n\nThe admin panel lets you create a post, pick a category, upload an image, and turn publishing on.",
            'status' => 1,
            'id_topic' => 6,
        ],
        [
            'id' => 10,
            'id_user' => 2,
            'title' => 'Node.js is not the backend, but the category still has a post',
            'img' => 'post_node.jpg',
            'content' => "The backend of this site is PHP, not Node. The Node.js category exists so the sidebar is not empty and so the category.php filter has something to show.\n\nIf you want a separate API later, you can add it. For now the frontend reads data straight from PHP pages.",
            'status' => 1,
            'id_topic' => 7,
        ],
        [
            'id' => 11,
            'id_user' => 1,
            'title' => 'Draft: this post does not appear on the home page',
            'img' => 'post_html.jpg',
            'content' => 'In the admin panel this record has status=0. It is missing from the home page and from published search results until an admin clicks publish.',
            'status' => 0,
            'id_topic' => 3,
        ],
    ];
    $insertPost = $pdo->prepare(
        'INSERT INTO posts (id, id_user, title, img, content, status, id_topic)
         VALUES (:id, :id_user, :title, :img, :content, :status, :id_topic)'
    );
    foreach ($posts as $post) {
        $insertPost->execute($post);
    }

    $guestEmma = 'emma@example.com';
    $guestAlex = 'alex@example.com';
    $comments = [
        [1, 1, $demoEmail, 'Thanks for the stack walkthrough. After docker compose up the home page opened right away, without OpenServer.'],
        [1, 1, $guestEmma, 'Can I change port 8080 if another project is already using it?'],
        [1, 1, $adminEmail, 'Yes. Update APP_PORT and APP_URL in .env, then run docker compose up -d. The carousel and links will pick up the new address.'],
        [1, 2, $demoEmail, 'Nice that PHP and MySQL run in separate containers. phpMyAdmin on 8081 came up immediately as well.'],
        [1, 2, $guestAlex, 'The schema loaded on its own. Just remember that after down -v you have to wait for the seeder again, because the database volume is gone.'],
        [1, 3, $guestEmma, 'Now it is clear why the carousel has exactly three posts: they belong to category id=8, as in the original code.'],
        [1, 3, $demoEmail, 'The paginated feed works too. Page two already shows other publications, not a copy of the carousel.'],
        [1, 4, $demoEmail, 'The password used to be hardcoded in connect.php. With .env it is much safer to show the project in class.'],
        [1, 4, $guestAlex, 'Do not set DB_HOST=localhost inside the container — PHP will not find MySQL. It has to be db.'],
        [0, 4, 'guest@example.com', 'This comment is not published yet. An admin must approve it in the comments section.'],
        [1, 5, $guestEmma, 'The carousel runs with no custom JS, only Bootstrap. Perfect for a lab: nothing to build.'],
        [1, 5, $demoEmail, 'scripts.js is in place too. If you add an alert on a button, it will pick up after a page refresh.'],
        [1, 6, $guestAlex, 'I changed the header color in style1.css and saw it on the home page at once. No image rebuild needed.'],
        [1, 6, $adminEmail, 'That is the idea: the code is mounted as a volume. Admin CSS lives separately and the home page does not touch it.'],
        [1, 7, $demoEmail, 'The comment form asks for a longer text, so a one-word "ok" will not pass. That filters empty spam.'],
        [1, 7, $guestEmma, 'The date under a comment comes from created_date. In the seeder it is set automatically by the MySQL timestamp.'],
        [1, 8, $guestAlex, 'The category sidebar is finally not empty. Clicking Frontend leaves only that post in the feed.'],
        [1, 8, $demoEmail, 'A search for MySQL also finds several articles. Handy for checking that the content is really in the database.'],
        [1, 9, $guestEmma, 'The admin panel feels like a simplified WP: posts, categories, users, comments. That is enough for the course.'],
        [0, 9, 'moderation@example.com', 'Comment pending moderation: a guest without admin rights is not published immediately, only after publish in the panel.'],
        [1, 10, $demoEmail, 'The Node.js category exists even though the server is PHP. That way the sidebar looks like a real blog, not a three-item demo.'],
        [1, 10, $guestAlex, 'If we add an API later, this post can be updated. For now everything is rendered with plain PHP.'],
    ];
    $insertComment = $pdo->prepare(
        'INSERT INTO comments (status, page, email, comment) VALUES (:status, :page, :email, :comment)'
    );
    foreach ($comments as [$status, $page, $email, $comment]) {
        $insertComment->execute([
            'status' => $status,
            'page' => $page,
            'email' => $email,
            'comment' => $comment,
        ]);
    }

    $pdo->exec('ALTER TABLE users AUTO_INCREMENT = 3');
    $pdo->exec('ALTER TABLE topics AUTO_INCREMENT = 9');
    $pdo->exec('ALTER TABLE posts AUTO_INCREMENT = 12');
    $pdo->exec('ALTER TABLE comments AUTO_INCREMENT = ' . (count($comments) + 1));
}

$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
if ($userCount > 0 && !$force) {
    seed_info('Database already has data. Skip seeding (use --force to reset demo data).');
    seed_placeholder_images(ROOT_PATH . '/assets/img/posts', false);
    exit(0);
}

if ($force) {
    seed_info('Force seed: clearing users, topics, posts, comments...');
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $pdo->exec('TRUNCATE TABLE comments');
    $pdo->exec('TRUNCATE TABLE posts');
    $pdo->exec('TRUNCATE TABLE topics');
    $pdo->exec('TRUNCATE TABLE users');
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}

seed_info('Seeding demo users, topics, posts and comments...');
seed_demo_data($pdo);
seed_placeholder_images(ROOT_PATH . '/assets/img/posts', $force);

seed_info('Seed complete.');
seed_info('Admin: ' . env_value('ADMIN_EMAIL', 'admin@myblog.local') . ' / ' . env_value('ADMIN_PASSWORD', 'admin123'));
seed_info('User:  ' . env_value('DEMO_EMAIL', 'demo@myblog.local') . ' / ' . env_value('DEMO_PASSWORD', 'user123'));
exit(0);
