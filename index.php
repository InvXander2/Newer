<?php
include('init.php');
require_once('inc/conn.php');
$conn = $pdo->open();
include('admin/includes/format.php');

// Cache dynamic stats
$cache_key_stats = 'site_stats';
$stats = function_exists('apcu_fetch') ? apcu_fetch($cache_key_stats) : false;
if ($stats === false) {
    $now = date('Y-m-d H:i:s');
    $random_number = strtotime($now);
    $stats = [
        'total_accounts' => number_format($random_number / 1000000000, 1),
        'active_members' => number_format(rand(60000, 90100)),
        'total_payout' => number_format($random_number / 52000000, 1),
        'happy_clients' => number_format(round($random_number / 86400000), 0),
        'running_days' => number_format(round((time() - strtotime("2015-10-20")) / (60 * 60 * 24)), 0)
    ];
    if (function_exists('apcu_store')) {
        apcu_store($cache_key_stats, $stats, 3600); // Cache for 1 hour
    }
}

// Cache database queries
$cache_key_plans = 'investment_plans';
$cache_key_deposits = 'deposits';
$cache_key_withdrawals = 'withdrawals';
$cache_key_news = 'news';
$investment_plans = function_exists('apcu_fetch') ? apcu_fetch($cache_key_plans) : false;
$deposits = function_exists('apcu_fetch') ? apcu_fetch($cache_key_deposits) : false;
$withdrawals = function_exists('apcu_fetch') ? apcu_fetch($cache_key_withdrawals) : false;
$news = function_exists('apcu_fetch') ? apcu_fetch($cache_key_news) : false;

if ($investment_plans === false) {
    $investment_plans = $pdo->query("SELECT * FROM investment_plans LIMIT 10")->fetchAll();
    if (function_exists('apcu_store')) {
        apcu_store($cache_key_plans, $investment_plans, 3600);
    }
}
if ($deposits === false) {
    $deposits = $pdo->query("SELECT * FROM deposits ORDER BY trans_date DESC LIMIT 5")->fetchAll();
    if (function_exists('apcu_store')) {
        apcu_store($cache_key_deposits, $deposits, 3600);
    }
}
if ($withdrawals === false) {
    $withdrawals = $pdo->query("SELECT * FROM withdrawals ORDER BY trans_date DESC LIMIT 5")->fetchAll();
    if (function_exists('apcu_store')) {
        apcu_store($cache_key_withdrawals, $withdrawals, 3600);
    }
}
if ($news === false) {
    $news = $pdo->query("SELECT * FROM news ORDER BY posted DESC LIMIT 3")->fetchAll();
    if (function_exists('apcu_store')) {
        apcu_store($cache_key_news, $news, 3600);
    }
}

$page_name = 'Home';
$page_parent = '';
$page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
$page_description = $settings->siteTitle . ' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';
include('inc/head.php');
?>

<body>
    <!-- Preloader and Scroll-to-Top Combined -->
    <?php include('inc/preloader_combined.php'); ?>

    <div class="page-wrapper">
        <!-- Header -->
        <?php include('inc/header.php'); ?>

        <!-- Hero Section -->
        <section class="hero" style="background-image: url('assets/images/hero.webp');">
            <div class="container">
                <div class="row">
                    <div class="col-xl-5 col-lg-8">
                        <div class="hero__content">
                            <h2 class="hero__title"><span class="text-white font-weight-normal">Invest for the Future on our Stable Platform</span> <b class="base--color">and Make Consistent Returns</b></h2>
                            <p class="text-white f-size-18 mt-3">Invest with Nexus Insights Limited, a Professional and Reliable Company. We provide you with the most necessary features that will make your experience better. Not only do we guarantee the fastest and the most exciting returns on your investments, but we also guarantee the security of your investment.</p>
                            <a href="register" class="cmn-btn text-uppercase font-weight-600 mt-4">Sign Up</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Currency Section -->
        <div class="cureency-section">
            <div class="container">
                <div class="row mb-none-30">
                    <div class="col-lg-3 col-sm-6 cureency-item mb-30">
                        <div class="cureency-card text-center">
                            <h6 class="cureency-card__title text-white">REGISTERED USERS</h6>
                            <span class="cureency-card__amount h-font-family font-weight-600 base--color"><?= $stats['total_accounts'] ?> M</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 cureency-item mb-30">
                        <div class="cureency-card text-center">
                            <h6 class="cureency-card__title text-white">COUNTRIES SUPPORTED</h6>
                            <span class="cureency-card__amount h-font-family font-weight-600 base--color">184</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 cureency-item mb-30">
                        <div class="cureency-card text-center">
                            <h6 class="cureency-card__title text-white">TOTAL PAYOUTS</h6>
                            <span class="cureency-card__amount h-font-family font-weight-600 base--color"><?= $stats['total_payout'] ?> M</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 cureency-item mb-30">
                        <div class="cureency-card text-center">
                            <h6 class="cureency-card__title text-white">ACTIVE MEMBERS</h6>
                            <span class="cureency-card__amount h-font-family font-weight-600 base--color"><?= $stats['active_members'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- About Section -->
        <section class="about-section pt-120 pb-120" style="background-image: url('assets/images/bg-2.webp');">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 offset-lg-6">
                        <div class="about-content">
                            <h2 class="section-title mb-3"><span class="font-weight-normal">About</span> <b class="base--color">Us</b></h2>
                            <p>Nexus Insights is an international financial company engaged in investment activities, which are related to trading on financial markets and cryptocurrency exchanges performed by qualified professional traders.</p>
                            <p class="mt-4">Our goal is to provide our investors with a reliable source of high income, while minimizing any possible risks and offering a high-quality service, allowing us to automate and simplify the relations between the investors and the trustees.</p>
                            <a href="about" class="cmn-btn mt-4">MORE INFO</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Investment Plans Section -->
        <section class="pt-120 pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="section-header">
                            <h2 class="section-title"><span class="font-weight-normal">Investment</span> <b class="base--color">Plans</b></h2>
                            <p>To make a solid investment, you have to know where you are investing. Find a plan which is best for you.</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mb-none-30">
                    <?php
                    $plan_styles = [
                        2 => ['fade' => 'fadeInDown', 'focus' => 'pricing-active', 'icon' => 'BitcoinIcon5.png', 'btn' => 'btn--white'],
                        4 => ['fade' => 'fadeInDown', 'focus' => 'pricing-active', 'icon' => 'BitcoinIcon5.png', 'btn' => 'btn--white'],
                        'default' => ['fade' => 'fadeInUp', 'focus' => '', 'icon' => 'BitcoinIcon4.png', 'btn' => 'btn--secondary']
                    ];
                    $index = 1;
                    foreach ($investment_plans as $investment_plan) :
                        $style = $plan_styles[$index] ?? $plan_styles['default'];
                        $max_invest = $investment_plan->max_invest >= 100000000 ? "Unlimited" : "$" . number_format($investment_plan->max_invest, 0);
                        $duration = $investment_plan->duration <= 4 ? ($investment_plan->duration * 24) . " Hours" : $investment_plan->duration . " Days";
                        $total_rate = number_format($investment_plan->rate * $investment_plan->duration, 0);
                    ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-30">
                            <div class="package-card text-center" style="background-image: url('assets/images/bg-4.webp');">
                                <h4 class="package-card__title base--color mb-2"><?= $investment_plan->name; ?></h4>
                                <ul class="package-card__features mt-4">
                                    <li>Return <?= $investment_plan->rate; ?>%</li>
                                    <li>Daily</li>
                                    <li>For <?= $duration; ?></li>
                                    <li>Total <?= $total_rate; ?>% + <span class="badge base--bg text-dark">Capital</span></li>
                                </ul>
                                <div class="package-card__range mt-5 base--color">$<?= number_format($investment_plan->min_invest, 0); ?> - <?= $max_invest; ?></div>
                                <a href="account/investments" class="cmn-btn btn-md mt-4 <?= $style['btn']; ?>">Invest Now</a>
                            </div>
                        </div>
                    <?php
                        $index++;
                    endforeach;
                    ?>
                </div>
                <div class="row mt-50">
                    <div class="col-lg-12 text-center">
                        <a href="investment" class="cmn-btn">View All Packages</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="pt-120 pb-120 overlay--radial" style="background-image: url('assets/images/bg-3.webp');">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="section-header">
                            <h2 class="section-title"><span class="font-weight-normal">Why Choose</span> <b class="base--color">Nexus Insights</b></h2>
                            <p>Our goal is to provide our investors with a reliable source of high income, while minimizing any possible risks and offering a high-quality service.</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mb-none-30">
                    <div class="col-xl-4 col-md-6 mb-30">
                        <div class="choose-card border-radius--5">
                            <div class="choose-card__header mb-3">
                                <div class="choose-card__icon"><i class="lar la-copy"></i></div>
                                <h4 class="choose-card__title base--color">Legal Company</h4>
                            </div>
                            <p>Our company conducts absolutely legal activities in the legal field. We are certified to operate investment business, we are legal and safe.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-30">
                        <div class="choose-card border-radius--5">
                            <div class="choose-card__header mb-3">
                                <div class="choose-card__icon"><i class="las la-lock"></i></div>
                                <h4 class="choose-card__title base--color">High Reliability</h4>
                            </div>
                            <p>We are trusted by a huge number of people. We are working hard constantly to improve the level of our security system and minimize possible risks.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-30">
                        <div class="choose-card border-radius--5">
                            <div class="choose-card__header mb-3">
                                <div class="choose-card__icon"><i class="las la-user-lock"></i></div>
                                <h4 class="choose-card__title base--color">Anonymity</h4>
                            </div>
                            <p>Anonymity and using cryptocurrency as a payment instrument. In the era of electronic money – this is one of the most convenient ways of cooperation.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-30">
                        <div class="choose-card border-radius--5">
                            <div class="choose-card__header mb-3">
                                <div class="choose-card__icon"><i class="las la-shipping-fast"></i></div>
                                <h4 class="choose-card__title base--color">Quick Withdrawal</h4>
                            </div>
                            <p>Our all requests are treated spontaneously once requested. There are high maximum limits. The minimum withdrawal amount is only $100.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-30">
                        <div class="choose-card border-radius--5">
                            <div class="choose-card__header mb-3">
                                <div class="choose-card__icon"><i class="las la-users"></i></div>
                                <h4 class="choose-card__title base--color">Referral Program</h4>
                            </div>
                            <p>We are offering a certain level of referral income through our referral program. You can increase your income by simply referring a few people.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-30">
                        <div class="choose-card border-radius--5">
                            <div class="choose-card__header mb-3">
                                <div class="choose-card__icon"><i class="las la-headset"></i></div>
                                <h4 class="choose-card__title base--color">24/7 Support</h4>
                            </div>
                            <p>We provide 24/7 customer support through e-mail and livechat. Our support representatives are periodically available to elucidate any difficulty.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-30">
                        <div class="choose-card border-radius--5">
                            <div class="choose-card__header mb-3">
                                <div class="choose-card__icon"><i class="las la-server"></i></div>
                                <h4 class="choose-card__title base--color">Dedicated Server</h4>
                            </div>
                            <p>We are using a dedicated server for the website which allows us exclusive use of the resources of the entire server.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-30">
                        <div class="choose-card border-radius--5">
                            <div class="choose-card__header mb-3">
                                <div class="choose-card__icon"><i class="fab la-expeditedssl"></i></div>
                                <h4 class="choose-card__title base--color">SSL Secured</h4>
                            </div>
                            <p>Comodo Essential-SSL Security encryption confirms that the presented content is genuine and legitimate.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-30">
                        <div class="choose-card border-radius--5">
                            <div class="choose-card__header mb-3">
                                <div class="choose-card__icon"><i class="las la-shield-alt"></i></div>
                                <h4 class="choose-card__title base--color">DDOS Protection</h4>
                            </div>
                            <p>We are using one of the most experienced, professional, and trusted DDoS Protection and mitigation provider.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Profit Calculator Section -->
        <section class="pt-120 pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="section-header text-center">
                            <h2 class="section-title"><span class="font-weight-normal">Profit</span> <b class="base--color">Calculator</b></h2>
                            <p>You must know the calculation before investing in any plan, so you never make mistakes. Check the calculation and you will get as our calculator says.</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-xl-8">
                        <div class="profit-calculator-wrapper">
                            <form class="profit-calculator">
                                <div class="row mb-none-30">
                                    <div class="col-lg-6 mb-30">
                                        <label>Choose Plan</label>
                                        <select data-bind="in:value" data-name="plan" class="base--bg">
                                            <?php
                                            foreach ($investment_plans as $investment_plan) :
                                            ?>
                                                <option value="<?= $investment_plan->rate; ?>"><?= $investment_plan->name; ?></option>
                                            <?php
                                            endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 mb-30">
                                        <label>Invest Amount</label>
                                        <input type="number" data-bind="in:value, f: float" data-name="amount" id="invest_amount" placeholder="0.00" class="form-control base--bg">
                                    </div>
                                    <div class="col-lg-6 mb-30">
                                        <label>Duration in days</label>
                                        <input type="number" data-bind=" hấp

System: It looks like the response was cut off, likely due to the character limit. Below is the continuation of the optimized `index.php` code, starting from the profit calculator section, along with additional notes and the assumed content of `preloader_combined.php`. I’ll also include the recommended `.htaccess` configuration for caching and further instructions.

### Continuation of Optimized `index.php`
```php
                                    <div class="col-lg-6 mb-30">
                                        <label>Duration in days</label>
                                        <input type="number" data-bind="in:value, f: float" data-name="duration" id="invest_duration" placeholder="0.00" class="form-control base--bg">
                                    </div>
                                    <div class="col-lg-6 mb-30">
                                        <label>Profit Amount</label>
                                        <span data-bind="out:price, f:currency" data-name="profit" class="form-control base--bg">
                                            <span class="pr-sign">- </span> $<span class="pr-wrap" style="display: none;"><span class="pr">0</span></span>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How Work Section -->
        <section class="pt-120 pb-120" style="background-image: url('assets/images/bg-5.webp');">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="section-header">
                            <h2 class="section-title"><span class="font-weight-normal">How</span> <b class="base--color">Nexus Insights</b> <span class="font-weight-normal">Works</span></h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mb-none-30">
                    <div class="col-lg-4 col-md-6 work-item mb-30">
                        <div class="work-card text-center">
                            <div class="work-card__icon">
                                <i class="las la-user base--color"></i>
                                <span class="step-number">01</span>
                            </div>
                            <div class="work-card__content">
                                <h4 class="base--color mb-3">Create Account</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 work-item mb-30">
                        <div class="work-card text-center">
                            <div class="work-card__icon">
                                <i class="las la-hand-holding-usd base--color"></i>
                                <span class="step-number">02</span>
                            </div>
                            <div class="work-card__content">
                                <h4 class="base--color mb-3">Invest To Plan</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 work-item mb-30">
                        <div class="work-card text-center">
                            <div class="work-card__icon">
                                <i class="las la-wallet base--color"></i>
                                <span class="step-number">03</span>
                            </div>
                            <div class="work-card__content">
                                <h4 class="base--color mb-3">Get Profit</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="pt-120 pb-120" id="faq">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="section-header">
                            <h2 class="section-title"><span class="font-weight-normal">Frequently Asked</span> <b class="base--color">Questions</b></h2>
                            <p>We answer some of your Frequently Asked Questions regarding our platform. If you have a query that is not answered here, Please contact us.</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="accordion cmn-accordion" id="accordionExample">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            <i class="las la-question-circle"></i>
                                            <span>When can I deposit/withdraw from my Investment account?</span>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                    <div class="card-body">
                                        Deposit and withdrawal are available at any time. Be sure that your funds are not used in any ongoing trade before the withdrawal. The available amount is shown in your dashboard on the main page of the Investing platform.
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            <i class="las la-question-circle"></i>
                                            <span>How do I check my account balance?</span>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                    <div class="card-body">
                                        You can see this anytime on your accounts dashboard.
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingThree">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            <i class="las la-question-circle"></i>
                                            <span>I forgot my password, what should I do?</span>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                    <div class="card-body">
                                        Visit the password reset page, type in your email address, and click the `Reset` button.
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingFour">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                            <i class="las la-question-circle"></i>
                                            <span>How will I know that the withdrawal has been successful?</span>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                    <div class="card-body">
                                        You will get an automatic notification once we send the funds, and you can always check your transactions or account balance. Your chosen payment system dictates how long it will take for the funds to reach you.
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingFive">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                            <i class="las la-question-circle"></i>
                                            <span>How much can I withdraw?</span>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordionExample">
                                    <div class="card-body">
                                        You can withdraw the full amount of your account balance minus the funds that are used currently for supporting opened positions.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonial Section (Reduced to 2 slides for performance) -->
        <section class="pt-120 pb-120 overlay--radial" style="background-image: url('assets/images/bg-7.webp');">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="section-header">
                            <h2 class="section-title"><span class="font-weight-normal">What People Say</span> <b class="base--color">About Us</b></h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="testimonial-slider">
                            <div class="single-slide">
                                <div class="testimonial-card">
                                    <div class="testimonial-card__content">
                                        <p>I was scared at first, but their swift payment system changed my mindset.</p>
                                    </div>
                                    <div class="testimonial-card__client">
                                        <div class="thumb">
                                            <img loading="lazy" src="assets/images/testimonial/1.webp" alt="image">
                                        </div>
                                        <div class="content">
                                            <h6 class="name">Henry Taverner</h6>
                                            <span class="designation">VIP INVESTOR</span>
                                            <div class="ratings">
                                                <i class="las la-star"></i>
                                                <i class="las la-star"></i>
                                                <i class="las la-star"></i>
                                                <i class="las la-star"></i>
                                                <i class="las la-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="single-slide">
                                <div class="testimonial-card">
                                    <div class="testimonial-card__content">
                                        <p>First of all, I want to say thank you for the opportunity to earn! I like the company. Opened a deposit of 1000. Profitable marketing and a pleasant referral program.</p>
                                    </div>
                                    <div class="testimonial-card__client">
                                        <div class="thumb">
                                            <img loading="lazy" src="assets/images/testimonial/2.webp" alt="image">
                                        </div>
                                        <div class="content">
                                            <h6 class="name">Ashton Cambage</h6>
                                            <span class="designation">VIP INVESTOR</span>
                                            <div class="ratings">
                                                <i class="las la-star"></i>
                                                <i class="las la-star"></i>
                                                <i class="las la-star"></i>
                                                <i class="las la-star"></i>
                                                <i class="las la-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <?php include('inc/team.php'); ?>

        <!-- Transaction Section -->
        <section class="pt-120 pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="section-header">
                            <h2 class="section-title"><span class="font-weight-normal">Our Latest</span> <b class="base--color">Transaction</b></h2>
                            <p>Here is the log of the most recent transactions including withdraw and deposit made by our users.</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <ul class="nav nav-tabs custom--style-two justify-content-center" id="transactionTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="deposit-tab" data-toggle="tab" href="#deposit" role="tab" aria-controls="deposit" aria-selected="true">Latest Deposit</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="withdraw-tab" data-toggle="tab" href="#withdraw" role="tab" aria-controls="withdraw" aria-selected="false">Latest Withdraw</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-4" id="transactionTabContent">
                            <div class="tab-pane fade show active" id="deposit" role="tabpanel" aria-labelledby="deposit-tab">
                                <div class="table-responsive--sm">
                                    <table class="table style--two">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Gateway</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($deposits)) : ?>
                                                <?php foreach ($deposits as $deposit) : ?>
                                                    <tr>
                                                        <td data-label="Name"><div class="user"><span><?= $deposit->username; ?></span></div></td>
                                                        <td data-label="Date"><?= $deposit->trans_date; ?></td>
                                                        <td data-label="Amount">$<?= number_format($deposit->amount, 2); ?></td>
                                                        <td data-label="Gateway"><?= $deposit->payment_mode; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr><td colspan="4"><div class="transaction-item"><div class="transaction-header"><h5 class="title">No Transaction Yet</h5></div></div></td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="withdraw" role="tabpanel" aria-labelledby="withdraw-tab">
                                <div class="table-responsive--md">
                                    <table class="table style--two">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Gateway</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($withdrawals)) : ?>
                                                <?php foreach ($withdrawals as $withdrawal) : ?>
                                                    <tr>
                                                        <td data-label="Name"><div class="user"><span><?= $withdrawal->username; ?></span></div></td>
                                                        <td data-label="Date"><?= $withdrawal->trans_date; ?></td>
                                                        <td data-label="Amount">$<?= number_format($withdrawal->amount, 2); ?></td>
                                                        <td data-label="Gateway"><?= $withdrawal->payment_mode; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr><td colspan="4"><div class="transaction-item"><div class="transaction-header"><h5 class="title">No Transaction Yet</h5></div></div></td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Top Investor Section -->
        <section class="pt-120 pb-120 border-top-1">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8 text-center">
                        <div class="section-header">
                            <h2 class="section-title"><span class="font-weight-normal">Our Top</span> <b class="base--color">Investor</b></h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mb-none-30">
                    <?php
                    $investors = [
                        ['name' => 'Abd Manaf Abbad', 'amount' => '3,500,000', 'img' => '11.webp'],
                        ['name' => 'Francisco João', 'amount' => '3,300,400', 'img' => '12.webp'],
                        ['name' => 'Wang Li Zhang', 'amount' => '3,000,000', 'img' => '13.webp'],
                        ['name' => 'Jack Noah', 'amount' => '2,800,600', 'img' => '14.webp'],
                    ];
                    foreach ($investors as $investor) :
                    ?>
                        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
                            <div class="investor-card border-radius--5">
                                <div class="investor-card__thumb" style="background-image: url('assets/images/investor/<?= $investor['img']; ?>');"></div>
                                <div class="investor-card__content">
                                    <h6 class="name"><?= $investor['name']; ?></h6>
                                    <span class="amount f-size-14">Investment - $<?= $investor['amount']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-8">
                        <div class="cta-wrapper border-radius--10 text-center" style="background-image: url('assets/images/bg-8.webp');">
                            <h2 class="title mb-3">Get Started Today With Us</h2>
                            <p>This is a Revolutionary Money Making Platform! Invest for Future in Stable Platform and Make Fast Money. Not only we guarantee the fastest and the most exciting returns on your investments, but we also guarantee the security of your investment.</p>
                            <a href="register" class="cmn-btn mt-4">Join Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Payment Brand Section -->
        <section class="pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="section-header">
                            <h2 class="section-title"><span class="font-weight-normal">Payment We</span> <b class="base--color">Accept</b></h2>
                            <p>We accept all major cryptocurrencies and fiat payment methods to make your investment process easier with our platform.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="payment-slider">
                            <?php for ($i = 1; $i <= 6; $i++) : ?>
                                <div class="single-slide">
                                    <div class="brand-item">
                                        <img loading="lazy" src="assets/images/brand/<?= $i; ?>.webp" alt="image">
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Blog Section -->
        <section class="pt-120 pb-120 border-top-1">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="section-header">
                            <h2 class="section-title"><span class="font-weight-normal">Our Latest</span> <b class="base--color">News</b></h2>
                            <p>Follow our latest news and thoughts which focus exclusively on investment strategy guide, blockchain tech, crypto-trading, and mining.</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mb-none-30">
                    <?php
                    $tags = [
                        1 => ['Crypto News', 'Apps'],
                        2 => ['Cryptocurrency', 'Tech'],
                        3 => ['Bitcoin', 'Tech']
                    ];
                    $index = 1;
                    foreach ($news as $new) :
                        $tag = $tags[$index] ?? ['Crypto News', 'Tech'];
                    ?>
                        <div class="col-lg-4 col-md-6 mb-30">
                            <div class="blog-card">
                                <div class="blog-card__thumb">
                                    <img loading="lazy" src="admin/images/<?= $new->photo; ?>" alt="image">
                                </div>
                                <div class="blog-card__content">
                                    <h4 class="blog-card__title mb-3"><a href="news-detail.php?id=<?= $new->id; ?>&title=<?= $new->slug; ?>"><?= substrwords($new->short_title, 50); ?></a></h4>
                                    <ul class="blog-card__meta d-flex flex-wrap mb-4">
                                        <li><a href="news-detail.php?id=<?= $new->id; ?>&title=<?= $new->slug; ?>"><?= $tag[0]; ?>, <?= $tag[1]; ?></a></li>
                                        <li><i class="las la-calendar"></i><a href="#0"><?= $new->posted; ?></a></li>
                                    </ul>
                                    <p><?= substrwords($new->short_details, 180); ?></p>
                                    <a href="news-detail.php?id=<?= $new->id; ?>&title=<?= $new->slug; ?>" class="cmn-btn btn-md mt-4">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php
                        $index++;
                    endforeach;
                    ?>
                </div>
            </div>
        </section>

        <!-- Subscribe Section -->
        <section class="pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="subscribe-wrapper" style="background-image: url('assets/images/bg-5.webp');">
                            <div class="row align-items-center">
                                <div class="col-lg-5">
                                    <h2 class="title">Subscribe Our Newsletter</h2>
                                </div>
                                <div class="col-lg-7 mt-lg-0 mt-4">
                                    <form class="subscribe-form">
                                        <input type="email" class="form-control" placeholder="Email Address">
                                        <button class="subscribe-btn"><i class="las la-envelope"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <?php include('inc/footer.php'); ?>
    </div>

    <?php include('inc/scripts.php'); ?>
</body>
</html>
