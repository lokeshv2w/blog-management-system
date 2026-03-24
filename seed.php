<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

try {
    // Basic array of 10 modern tech categories
    $categories = [
        'Web Development', 'Artificial Intelligence', 'Machine Learning', 
        'Data Science', 'Cloud Computing', 'Cybersecurity', 
        'Mobile Development', 'DevOps', 'Blockchain', 'UI/UX Design'
    ];

    $category_map = []; // Map name to inserted/retrieved ID
    
    // Insert categories if they don't exist
    $stmtCategory = $pdo->prepare("INSERT IGNORE INTO categories (name, slug) VALUES (?, ?)");
    foreach ($categories as $catName) {
        $slug = slugify($catName);
        $stmtCategory->execute([$catName, $slug]);
        
        // Get the ID (either newly inserted or existing)
        $stmtGet = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmtGet->execute([$slug]);
        if ($row = $stmtGet->fetch()) {
            $category_map[$catName] = $row['id'];
        }
    }
    echo "<h3>&check; 10 Categories checked and seeded successfully.</h3>";

    // Get admin user ID 
    $stmtUser = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    $user = $stmtUser->fetch();
    $user_id = $user ? $user['id'] : 1; // Fallback to 1 if user table is somehow empty

    // Array of 10 sample posts
    $posts = [
        [
            'title' => 'Getting Started with Modern Web Development',
            'content' => '<p>The web development landscape is constantly evolving. In this guide, we explore the top frameworks to learn this year such as Next.js, SvelteKit, and Vue.</p><h2>The Core Technologies</h2><p>Remember that HTML, CSS, and vanilla JavaScript form the foundation of everything you build.</p>',
            'cat' => 'Web Development'
        ],
        [
            'title' => 'The Rise of Generative AI in Code',
            'content' => '<p>Artificial Intelligence is changing how we write code. Tools like GitHub Copilot and ChatGPT are acting as pair programmers, helping developers ship faster than ever.</p><p>Instead of manually looking up documentation, developers can now describe the logic they want, and the AI will generate the initial scaffolding.</p>',
            'cat' => 'Artificial Intelligence'
        ],
        [
            'title' => 'Understanding Machine Learning Algorithms',
            'content' => '<p>Machine learning doesn\'t have to be intimidating. We break down standard algorithms like linear regression, decision trees, and neural networks into plain English.</p><p>At its core, it\'s just math recognizing patterns in large sets of data.</p>',
            'cat' => 'Machine Learning'
        ],
        [
            'title' => 'Why Data Science is the Sexiest Job of the 21st Century',
            'content' => '<p>With data growing exponentially, companies desperately need experts who can analyze, interpret, and drive business decisions based on massive datasets.</p><ul><li>Python</li><li>R</li><li>SQL</li><li>Tableau</li></ul>',
            'cat' => 'Data Science'
        ],
        [
            'title' => 'AWS vs Azure vs Google Cloud: Which to Choose?',
            'content' => '<p>Cloud computing has revolutionized infrastructure. We compare the big three providers based on pricing, ease of use, and enterprise capabilities.</p><p>While AWS holds the market share, Azure integrates seamlessly with Microsoft enterprise products, and GCP offers top-tier machine learning tools.</p>',
            'cat' => 'Cloud Computing'
        ],
        [
            'title' => 'Top 5 Cybersecurity Threats in 2024',
            'content' => '<p>Cyber threats are becoming more sophisticated. Learn about ransomware attacks, zero-day vulnerabilities, phishing schemes, and how to defend against them.</p><h2>The Best Defense</h2><p>User education remains the strongest defense against social engineering attacks.</p>',
            'cat' => 'Cybersecurity'
        ],
        [
            'title' => 'Building Cross-Platform Mobile Apps with React Native',
            'content' => '<p>React Native gives you the power to build natively-rendering mobile applications for iOS and Android using JavaScript and React.</p><p>By maintaining a single codebase, companies save significant development time and resources while still delivering a native feel.</p>',
            'cat' => 'Mobile Development'
        ],
        [
            'title' => 'Introduction to Docker and Kubernetes',
            'content' => '<p>Containerization and orchestration are crucial for modern DevOps. We simplify the concepts of containers and how Kubernetes manages them at scale.</p><p>Imagine Docker as the shipping container, and Kubernetes as the port manager automating the movement of those ships.</p>',
            'cat' => 'DevOps'
        ],
        [
            'title' => 'Beyond Cryptocurrency: The Future of Blockchain Tech',
            'content' => '<p>While Bitcoin introduced blockchain to the world, its underlying technology has applications ranging from supply chain transparency to secure voting systems.</p><p>A decentralized ledger ensures that no single entity has control over the data, making it essentially immutable.</p>',
            'cat' => 'Blockchain'
        ],
        [
            'title' => 'Designing User-Centric Interfaces with Figma',
            'content' => '<p>Great UI/UX is the key to software adoption. Learn how Figma has become the industry standard for collaborative design and prototyping.</p><p>Its cloud-based nature allows real-time collaboration between designers and developers, shrinking the gap between a mock-up and production code.</p>',
            'cat' => 'UI/UX Design'
        ]
    ];

    // Insert posts ignoring duplicate titles/slugs
    $stmtPost = $pdo->prepare("INSERT IGNORE INTO posts (title, slug, content, category_id, user_id, status) VALUES (?, ?, ?, ?, ?, 'published')");

    foreach ($posts as $post) {
        $slug = slugify($post['title']);
        $cat_id = $category_map[$post['cat']] ?? null;
        $stmtPost->execute([$post['title'], $slug, $post['content'], $cat_id, $user_id]);
    }

    echo "<h3>&check; 10 Posts checked and seeded successfully.</h3>";
    echo "<br><br><a href='index.php' style='padding: 10px 20px; background: #4F46E5; color: white; text-decoration: none; border-radius: 5px; font-family: sans-serif;'>Go to Homepage</a>";

} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Error seeding database:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
