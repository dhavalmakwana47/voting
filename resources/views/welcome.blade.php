<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>India E-Voting - Empowering Creditor Participation in Insolvency Proceedings</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('homepage/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('homepage/assets/css/main.css') }}" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WVR1139744"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-WVR1139744');
    </script>
  
</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="{{ route('index') }}" class="logo d-flex align-items-center me-auto">
                <img src="{{ asset('homepage/assets/img/logo.png') }}" alt="">
                <!-- <h1 class="sitename">India E-Voting</h1> -->
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ route('index') }}" class="">Home</a></li>
                    <li><a href="#about">About us</a></li>
                    <li><a href="#contact">Contact us</a></li>
                    <li><a href="{{ route('login') }}">Admin Login</a></li>

                    <!-- <li><a href="index.html#features">Features</a></li>
                    <li><a href="index.html#services">Services</a></li>
                    <li><a href="index.html#pricing">Pricing</a></li>
                    <li class="dropdown"><a href="#"><span>Dropdown</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="#">Dropdown 1</a></li>
                            <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i
                                        class="bi bi-chevron-down toggle-dropdown"></i></a>
                                <ul>
                                    <li><a href="#">Deep Dropdown 1</a></li>
                                    <li><a href="#">Deep Dropdown 2</a></li>
                                    <li><a href="#">Deep Dropdown 3</a></li>
                                    <li><a href="#">Deep Dropdown 4</a></li>
                                    <li><a href="#">Deep Dropdown 5</a></li>
                                </ul>
                            </li>
                            <li><a href="#">Dropdown 2</a></li>
                            <li><a href="#">Dropdown 3</a></li>
                            <li><a href="#">Dropdown 4</a></li>
                        </ul>
                    </li>
                    <li><a href="index.html#contact">Contact</a></li>  -->
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted" href="{{ route('member.login') }}">Voter Login</a> 

        </div>
    </header>

    <main class="main">

        <!-- Hero Section -->
        <section id="hero" class="hero section">
            <div class="hero-bg">
                <img src="{{ asset('homepage/assets/img/hero-bg-light.webp') }}" alt="">
            </div>
            <div class="container text-center">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <h1 data-aos="fade-up" class="">Welcome to <span>E-Voting</span></h1>
                    <p data-aos="fade-up" data-aos-delay="100" class="">Empowering Creditor Participation in Insolvency Proceedings<br></p>
                    <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
                        <a href="#about" class="btn-get-started">Get Started</a>
                        <!-- <a href="#"
                            class="glightbox btn-watch-video d-flex align-items-center"><i
                                class="bi bi-play-circle"></i><span>Watch Video</span></a> -->
                    </div>
                    <img src="{{ asset('homepage/assets/img/hero-services-img.webp') }}" class="img-fluid hero-img"
                        alt="" data-aos="zoom-out" data-aos-delay="300">
                </div>
            </div>

        </section><!-- /Hero Section -->

        <!-- Featured Services Section -->
        <section id="featured-services" class="featured-services section">

            <div class="container">

                <div class="row gy-4">

                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-item d-flex">
                            <div class="icon flex-shrink-0"><i class="bi bi-briefcase"></i></div>
                            <div>
                                <h4 class="title"><a href="#" class="stretched-link">IBC E-Voting</a></h4>
                                <p class="description">We're committed to facilitating transparent and efficient resolution processes under the Insolvency and Bankruptcy Code.</p>
                            </div>
                        </div>
                    </div>
                    <!-- End Service Item -->

                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-item d-flex">
                            <div class="icon flex-shrink-0"><i class="bi bi-card-checklist"></i></div>
                            <div>
                                <h4 class="title"><a href="#" class="stretched-link">Club E-Voting</a></h4>
                                <p class="description">We believe in the power of democracy at every level, including within clubs and organizations.</p>
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-item d-flex">
                            <div class="icon flex-shrink-0"><i class="bi bi-bar-chart"></i></div>
                            <div>
                                <h4 class="title"><a href="#" class="stretched-link">Society E-Voting</a>
                                </h4>
                                <p class="description">Society E-Voting is your gateway to democratic participation in the digital age.</p>
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                </div>

            </div>

        </section><!-- /Featured Services Section -->

        <!-- About Section -->
        <section id="about" class="about section">
    <div class="container">
        <p class="who-we-are"><Center><b><h1><u>Who We Are</u></h1> </b></Center></p>
        <h3>India E-Voting – Redefining Digital Democracy Through Innovation</h3>

        <p class="fst-italic">
            At India E-Voting, we harness the power of advanced technology to deliver secure, transparent, and reliable electronic voting solutions. Our mission is to empower organizations, institutions, and communities by making decision-making processes more accessible and efficient.
        </p>

        <p class="fst-italic">
            Founded with a vision to modernize democratic participation, we have evolved into a trusted technology partner for e-voting solutions across diverse sectors. Our platform is designed to ensure integrity, accuracy, and confidence in every vote cast.
        </p>

        <p class="fst-italic">
            What truly sets us apart is our commitment to excellence and user trust. We work closely with our clients to understand their unique requirements and deliver customized, compliant, and scalable e-voting systems that exceed expectations.
        </p>

        <p class="fst-italic">
            Our comprehensive offerings include secure e-voting platforms, system integration, data protection, and technical support—built to meet the highest standards of reliability and regulatory compliance.
        </p>

        <p class="fst-italic">
            At India E-Voting, we are more than a technology provider—we are a catalyst for transparent governance and digital transformation. Join us as we shape the future of voting with innovation, integrity, and trust.
        </p>
    </div>
</section>


        <!-- Features Section -->
        <section id="features-details" class="features-details section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2 class="">Type's Of E-Votign</h2>
                <p>Trust and confidentiality are paramount in insolvency proceedings.</p>
            </div>

            <div class="container">

                <div class="row gy-4 justify-content-between features-item">

                    <div class="col-lg-5 d-flex align-items-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="content">
                             <h3>Welcome to IBC E-Vote: Empowering Creditor Participation in Insolvency Proceedings</h3>
                            <p>
                                At IBC EVote, we're committed to facilitating transparent and efficient resolution processes under the Insolvency and Bankruptcy Code. Our electronic voting platform empowers creditors to participate in crucial decision-making, ensuring their voices are heard and their interests are protected throughout insolvency proceedings.
                            </p>
                            
                        </div>
                    </div>
                    
                    <div class="col-lg-5 d-flex align-items-center order-2 order-lg-1" data-aos="fade-up"
                        data-aos-delay="100">

                        <div class="content">
                            <h3>Welcome to Society eVoting: Where Your Voice Shapes Our Future</h3>
                            <p>
                                Society eVoting is your gateway to democratic participation in the digital age. We're committed to empowering citizens like you to make informed decisions and have a direct impact on the issues that matter most to our society. With our secure and user-friendly electronic voting platform, you can exercise your right to vote conveniently and confidently from anywhere in the world.
                            </p>
                            
                        </div>
                        </div>
                        
                         <div class="col-lg-5 d-flex align-items-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="content">
                             <h3>Welcome to Company Law E-Voting: Simplifying Corporate Governance</h3>
                            <p>
                                At Company Law E-Voting, we understand the importance of corporate governance and shareholder participation in decision-making processes. Our advanced electronic voting platform is designed to streamline the voting process for shareholders, ensuring transparency, efficiency, and compliance with company law regulations.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex align-items-center order-2 order-lg-1" data-aos="fade-up"
                        data-aos-delay="100">

                        <div class="content">
                            <h3>Welcome to University E-Vote: Your Voice, Your Choice</h3>
                            <p>
                                At University E-Vote, we believe in empowering students to actively participate in the democratic process of their academic community. Our secure and user-friendly e-voting platform is designed to make it easy for you to cast your vote and have your say in important university decisions.
                            </p>
                            
                        </div>

                </div><!-- Features Item -->

              <div class="col-lg-5 d-flex align-items-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="content">
                             <h3>Welcome to NGO E-Vote: Empowering Change Through Digital Democracy</h3>
                            <p>
                                NGOeVote is more than just a platform; it's a movement towards a more inclusive and participatory society. Our mission is to empower individuals and communities to have a direct say in the decisions that affect them most. With our secure and accessible eVoting platform, we're bridging the gap between citizens and their voices, fostering transparency, and driving positive change.
                            </p>
                            
                        </div>
                    </div>
                    
                    <div class="col-lg-5 d-flex align-items-center order-2 order-lg-1" data-aos="fade-up"
                        data-aos-delay="100">

                        <div class="content">
                            <h3>Welcome to Club E-Vote: Your Platform for Inclusive Decision-Making</h3>
                            <p>
                               At Club EVote, we believe in the power of democracy at every level, including within clubs and organizations. Our platform is designed to facilitate fair, secure, and efficient electronic voting processes, enabling members to participate in decision-making with ease and transparency.
                            </p>
                           
                        </div>

                    </div>
                    
            
           

        </section><!-- /Features Details Section -->

     
        <!-- Contact Section -->
        <section id="contact" class="contact section">
    <div class="container section-title" data-aos="fade-up">
        <h2>Get in Touch</h2>
        <p>We’re here to help. Reach out to us for any queries, support, or collaboration.</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4 justify-content-center">

            <div class="col-lg-4 col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center text-center"
                     data-aos="fade-up" data-aos-delay="200">
                    <i class="bi bi-envelope-open"></i>
                    <h3>Official Email</h3>
                    <p><a href="mailto:info@indiaevoting.com">info@indiaevoting.com</a></p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center text-center"
                     data-aos="fade-up" data-aos-delay="300">
                    <i class="bi bi-telephone-forward"></i>
                    <h3>Call Us</h3>
                    <p><a href="tel:+917990822351">+91 79908 22351</a></p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center text-center"
                     data-aos="fade-up" data-aos-delay="400">
                    <i class="bi bi-envelope-at"></i>
                    <h3>Support Email</h3>
                    <p><a href="mailto:indiaevoting024@gmail.com">indiaevoting024@gmail.com</a></p>
                </div>
            </div>

        </div>
    </div>
</section>


    </main>

    <footer id="footer" class="">
        <div class="container copyright text-center mt-1">
            <p>©2024<span> Copyright</span><strong class="px-2 sitename">India E-voting</strong>
            <span>All Rights Reserved</span>
            <strong class="px-1 sitename">Apexrise Consultant and E-Service</strong>
            </p>
            </div>
    </footer>


    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('homepage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('homepage/assets/js/main.js') }}"></script>

</body>

</html>
