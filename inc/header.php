<header class="header">
      <div class="header__bottom">
        <div class="container">
          <nav class="navbar navbar-expand-xl p-0 align-items-center">
            <a class="site-logo site-title" href="<?= $baseurl; ?>"><img src="assets/images/logo.png" alt="site-logo"></a>
            <ul class="account-menu mobile-acc-menu">
              <li class="icon">
                <a href="login"><i class="las la-user"></i></a>
              </li>
            </ul> 
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="menu-toggle"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav main-menu m-auto">
                <li <?php echo ( $page_name == 'Home' || $page_parent == 'Home' ) ? 'class="active"' : ''; ?>> 
                  <a href="<?= $baseurl; ?>">Home</a>
                </li>
                <li <?php echo ( $page_name == 'About Us' || $page_parent == 'About Us' ) ? 'class="active"' : ''; ?>> 
                  <a href="about">About Us</a>
                </li>
                <li <?php echo ( $page_name == 'Investment Plan' || $page_parent == 'Investment Plan' ) ? 'class="active"' : ''; ?>> 
                  <a href="investment">Plan</a>
                </li>
                <li <?php echo ( $page_name == 'FAQ' || $page_parent == 'FAQ' ) ? 'class="active"' : ''; ?>>
                    <a href="<?= $baseurl; ?>#faq">Faqs</a>
                </li>
                <li <?php echo ( $page_name == 'News' || $page_parent == 'News' ) ? 'class="active"' : ''; ?>>
                    <a href="news">News</a>
                </li>
                <li <?php echo ( $page_name == 'Account' || $page_parent == 'Account' ) ? 'class="active"' : ''; ?> class="menu_has_children"><a href="javascript:void(0)">Account</a>
                  <ul class="sub-menu">
                    <li><a href="login">Login</a></li>
                    <li><a href="register">Register</a></li>
                  </ul>
                </li>
                <li <?php echo ( $page_name == 'Contact Us' || $page_parent == 'Contact Us' ) ? 'class="active"' : ''; ?>>
                    <a href="contact">Contact</a>
                </li>
              </ul>
              <div class="nav-right">
                <ul class="navbar-nav main-menu m-auto">
                  <li><?php include('inc/translate2.php'); ?></li>
                </ul>
                <ul class="account-menu ml-3">
                  <li class="icon">
                    <a href="login"><i class="las la-user"></i></a>
                  </li>
                </ul>
                <div class="translate">
                  <?php include('inc/translate.php'); ?>                  
                </div>
              </div>
            </div> 
          </nav>
        </div>
      </div><!-- header__bottom end -->
    </header>
