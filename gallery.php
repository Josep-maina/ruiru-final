<?php include 'includes/header.php'; ?>

<!-- Gallery Hero Section -->
<section class="gallery-hero">
    <div class="hero-overlay"></div>
    <img src="img/about-hero-bg.jpg" class="hero-bg-image" alt="Gallery Background">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-12 text-center hero-content">
                <h1 class="display-4 fw-bold text-white mb-3">Our Gallery</h1>
                <p class="lead text-white">Explore the vibrant life at Ruiru Technical and Vocational College</p>
                <div class="hero-breadcrumb mt-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-warning">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Gallery</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Filters -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="gallery-filters text-center">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="courses">Courses</button>
                    <button class="filter-btn" data-filter="events">Events</button>
                    <button class="filter-btn" data-filter="campus">Campus</button>
                    <button class="filter-btn" data-filter="sports">Sports</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Grid -->
<section class="py-5">
    <div class="container">
        <div class="row g-4" id="gallery-grid">
            
            <!-- Courses Gallery Items -->
            <div class="col-lg-4 col-md-6 gallery-item" data-category="courses">
                <div class="gallery-card">
                    <img src="img/ict1.jpg" alt="ICT Lab" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>ICT Lab</h5>
                            <p>Modern computer laboratory for practical training</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/ict1.jpg', 'ICT Lab', 'Modern computer laboratory for practical training')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="courses">
                <div class="gallery-card">
                    <img src="img/catering1 - Copy.jpg" alt="Food Production Service" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Food Production - Service</h5>
                            <p>Students learning professional food service techniques</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/catering1 - Copy.jpg', 'Food Production - Service', 'Students learning professional food service techniques')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="courses">
                <div class="gallery-card">
                    <img src="img/catering2.jpg" alt="Food Production Service" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Food Production - Service</h5>
                            <p>Hands-on training in culinary arts</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/catering2.jpg', 'Food Production - Service', 'Hands-on training in culinary arts')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="courses">
                <div class="gallery-card">
                    <img src="img/food1.jpg" alt="Food Production Practicals" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Food Production - Service Practicals</h5>
                            <p>Practical cooking sessions in our modern kitchen</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/food1.jpg', 'Food Production - Service Practicals', 'Practical cooking sessions in our modern kitchen')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="courses">
                <div class="gallery-card">
                    <img src="img/food2.jpg" alt="Food Production Practicals" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Food Production - Service Practicals</h5>
                            <p>Students mastering culinary techniques</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/food2.jpg', 'Food Production - Service Practicals', 'Students mastering culinary techniques')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="courses">
                <div class="gallery-card">
                    <img src="img/plumbing2.jpg" alt="Plumbing Practicals" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Plumbing Practicals</h5>
                            <p>Hands-on plumbing training sessions</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/plumbing2.jpg', 'Plumbing Practicals', 'Hands-on plumbing training sessions')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="courses">
                <div class="gallery-card">
                    <img src="img/electrical2.jpg" alt="Electrical Practicals" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Electrical Practicals</h5>
                            <p>Students learning electrical installation techniques</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/electrical2.jpg', 'Electrical Practicals', 'Students learning electrical installation techniques')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="courses">
                <div class="gallery-card">
                    <img src="img/building2.jpg" alt="Masonry Practicals" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Masonry Practicals</h5>
                            <p>Building and construction training</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/building2.jpg', 'Masonry Practicals', 'Building and construction training')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events Gallery Items -->
            <div class="col-lg-4 col-md-6 gallery-item" data-category="events">
                <div class="gallery-card">
                    <img src="img/tvet fair.jpg" alt="TVET Innovation Award" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Winning Item 2024 TVET Innovation</h5>
                            <p>Our award-winning innovation at TVET fair</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/tvet fair.jpg', 'Winning Item 2024 TVET Innovation', 'Our award-winning innovation at TVET fair')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="events">
                <div class="gallery-card">
                    <img src="img/tvetstand.jpg" alt="TVET Showcase Stand" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Our Showcasing Stand at TVET Innovations</h5>
                            <p>Displaying our college achievements</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/tvetstand.jpg', 'Our Showcasing Stand at TVET Innovations', 'Displaying our college achievements')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="events">
                <div class="gallery-card">
                    <img src="img/Tree planting - Copy.jpg" alt="Greening Initiative" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Greening Initiative</h5>
                            <p>Environmental conservation activities</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/Tree planting - Copy.jpg', 'Greening Initiative', 'Environmental conservation activities')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="events">
                <div class="gallery-card">
                    <img src="img/thika1.jpg" alt="TVET at 100 Celebrations" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>TVET at 100 Celebrations</h5>
                            <p>Celebrating 100 years of TVET education</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/thika1.jpg', 'TVET at 100 Celebrations', 'Celebrating 100 years of TVET education')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campus Gallery Items -->
            <div class="col-lg-4 col-md-6 gallery-item" data-category="campus">
                <div class="gallery-card">
                    <img src="img/front.jpg" alt="School Front" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>School Front</h5>
                            <p>Main entrance of our beautiful campus</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/front.jpg', 'School Front', 'Main entrance of our beautiful campus')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="campus">
                <div class="gallery-card">
                    <img src="img/gate.jpg" alt="College Gate" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>College Gate</h5>
                            <p>Welcome to Ruiru Technical and Vocational College</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/gate.jpg', 'College Gate', 'Welcome to Ruiru Technical and Vocational College')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="campus">
                <div class="gallery-card">
                    <img src="img/sunset.jpg" alt="Evening Sunset" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Evening Sunset</h5>
                            <p>Beautiful sunset view from our campus</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/sunset.jpg', 'Evening Sunset', 'Beautiful sunset view from our campus')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sports Gallery Items -->
            <div class="col-lg-4 col-md-6 gallery-item" data-category="sports">
                <div class="gallery-card">
                    <img src="img/1000012313.jpg" alt="Gents Football Team" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Gents Football Team</h5>
                            <p>Our talented male football team</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/1000012313.jpg', 'Gents Football Team', 'Our talented male football team')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="sports">
                <div class="gallery-card">
                    <img src="img/slide3.jpg" alt="Ladies Football Team" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Ladies Football Team</h5>
                            <p>Our skilled female football team</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/slide3.jpg', 'Ladies Football Team', 'Our skilled female football team')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 gallery-item" data-category="sports">
                <div class="gallery-card">
                    <img src="img/play1.jpg" alt="Football Training" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-content">
                            <h5>Gents Playing Football</h5>
                            <p>Students enjoying football training</p>
                            <button class="btn btn-warning btn-sm" onclick="openLightbox('img/play1.jpg', 'Gents Playing Football', 'Students enjoying football training')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Load More Button -->
        <div class="text-center mt-5">
            <button class="btn btn-success btn-lg" id="loadMoreBtn">
                <i class="fas fa-plus"></i> Load More Photos
            </button>
        </div>
    </div>
</section>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-labelledby="lightboxModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="lightboxModalLabel">Image Title</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="/placeholder.svg" alt="" class="img-fluid" id="lightboxImage">
                <div class="p-3">
                    <p class="text-white mb-0" id="lightboxDescription">Image description</p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-warning" onclick="downloadImage()">
                    <i class="fas fa-download"></i> Download
                </button>
                <button type="button" class="btn btn-success" onclick="shareImage()">
                    <i class="fas fa-share"></i> Share
                </button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
