<?php
// includes/data.php
// This file serves as a mock database for our application.
// We use a static array to simulate what we would normally fetch from a database (like MySQL).

// Array of categories for our filter pill links
$categories = [
    'design' => 'Brand & Design',
    'dev' => 'Development',
    'writing' => 'Copywriting'
];

// Array of services offered on Tandem
$services = [
    [
        'id' => 1,
        'category' => 'design',
        'title' => 'Minimalist Logo & Brand Identity System',
        'freelancer_name' => 'Elena Rodriguez',
        'price' => 1200,
        'rating' => 5.0,
        'reviews' => 42,
        'description' => 'Complete brand guidelines, logo suite, and typography tailored for premium D2C brands. Includes a style guide, social media assets, and business card designs.'
    ],
    [
        'id' => 2,
        'category' => 'dev',
        'title' => 'Custom Next.js E-Commerce Storefront',
        'freelancer_name' => 'James Chen',
        'price' => 3500,
        'rating' => 4.9,
        'reviews' => 18,
        'description' => 'A fully custom, high-performance e-commerce frontend integrated with Shopify or Swell. Built for speed and high conversion rates.'
    ],
    [
        'id' => 3,
        'category' => 'writing',
        'title' => 'SEO-Optimized Landing Page Copy',
        'freelancer_name' => 'Sarah Jenkins',
        'price' => 450,
        'rating' => 4.8,
        'reviews' => 89,
        'description' => 'Compelling, conversion-focused copywriting for your primary landing page. Includes competitor research and keyword optimization.'
    ],
    [
        'id' => 4,
        'category' => 'design',
        'title' => 'Modern UI/UX App Redesign',
        'freelancer_name' => 'Marcus Thorne',
        'price' => 2800,
        'rating' => 5.0,
        'reviews' => 12,
        'description' => 'A complete visual overhaul of your mobile or web app focusing on user experience, modern aesthetics, and accessibility.'
    ],
    [
        'id' => 5,
        'category' => 'dev',
        'title' => 'Automated API Integrations (Zapier/Custom)',
        'freelancer_name' => 'Fatima Al-Sayed',
        'price' => 800,
        'rating' => 4.7,
        'reviews' => 34,
        'description' => 'Streamline your business operations by connecting your SaaS tools. I build custom API scripts or complex Zapier workflows.'
    ],
    [
        'id' => 6,
        'category' => 'writing',
        'title' => 'Monthly Tech Blog Articles (4 Posts)',
        'freelancer_name' => 'David Kim',
        'price' => 1000,
        'rating' => 4.9,
        'reviews' => 55,
        'description' => 'Four well-researched, technical blog articles (1500+ words each) tailored for developer and B2B SaaS audiences.'
    ],
    [
        'id' => 7,
        'category' => 'design',
        'title' => 'Premium Presentation Deck Design',
        'freelancer_name' => 'Elena Rodriguez',
        'price' => 600,
        'rating' => 5.0,
        'reviews' => 27,
        'description' => 'Transform your rough slides into a persuasive, investor-ready pitch deck with custom illustrations and premium typography.'
    ],
    [
        'id' => 8,
        'category' => 'dev',
        'title' => 'WordPress Custom Theme Development',
        'freelancer_name' => 'James Chen',
        'price' => 1800,
        'rating' => 4.8,
        'reviews' => 61,
        'description' => 'A custom WordPress theme built from scratch (no bloated builders). Fast, secure, and tailored exactly to your brand.'
    ]
];
