@extends('website.main.landpage')

@section('content')

<style>
/* ── Same tokens as landpage ─────────────────────────────── */
:root {
    --paper:    #faf8f4;
    --paper-2:  #f2efe7;
    --ink:      #1c1a17;
    --ink-mid:  #635d52;
    --ink-soft: #a59c8c;
    --line:     #e4e0d8;
    --clay:     #b5562e;
    --clay-dim: #e9d2c5;
    --moss:     #5c6650;
    --font-display: 'Fraunces', Georgia, serif;
    --font-mono:    'Space Grotesk', 'Courier New', monospace;
    --ease: cubic-bezier(.4,0,.2,1);
}

*, *::before, *::after { box-sizing: border-box; }
a { text-decoration: none; color: inherit; }
img { display: block; max-width: 100%; }

.catalogue { font-family: var(--font-mono); color: var(--ink); background: var(--paper); }
.wrap { max-width: 1180px; margin: 0 auto; padding: 0 1.75rem; }

/* ── Breadcrumb ─────────────────────────────────────────── */
.breadcrumb-bar {
    padding: 1.1rem 0;
    border-bottom: 1px solid var(--line);
    font-size: .72rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--ink-soft);
}
.breadcrumb-bar .wrap { display: flex; align-items: center; gap: .5rem; }
.breadcrumb-bar a { color: var(--ink-mid); transition: color .2s; }
.breadcrumb-bar a:hover { color: var(--clay); }
.breadcrumb-bar i { font-size: .65rem; }

/* ── Page header ────────────────────────────────────────── */
.guide-head {
    padding: 3.5rem 0 3rem;
    border-bottom: 1px solid var(--line);
}
.guide-eyebrow {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--clay);
    display: block;
    margin-bottom: .75rem;
}
.guide-title {
    font-family: var(--font-display);
    font-size: clamp(2rem, 5vw, 3.25rem);
    font-weight: 600;
    line-height: 1.1;
    margin: 0 0 .75rem;
}
.guide-intro {
    color: var(--ink-mid);
    max-width: 58ch;
    font-size: .95rem;
    line-height: 1.7;
}

/* ── Unit toggle ────────────────────────────────────────── */
.unit-toggle {
    display: inline-flex;
    border: 1px solid var(--line);
    margin-top: 1.5rem;
}
.unit-btn {
    background: none;
    border: none;
    padding: .5rem 1.25rem;
    font-family: var(--font-mono);
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    cursor: pointer;
    color: var(--ink-soft);
    transition: background .2s, color .2s;
}
.unit-btn.active {
    background: var(--ink);
    color: var(--paper);
}

/* ── Section shell ──────────────────────────────────────── */
.guide-section { padding: 3.5rem 0; border-bottom: 1px solid var(--line); }
.section-eyebrow {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--clay);
    display: block;
    margin-bottom: .6rem;
}
.section-title {
    font-family: var(--font-display);
    font-size: clamp(1.5rem, 3vw, 2.1rem);
    font-weight: 600;
    line-height: 1.15;
    margin: 0 0 .5rem;
}
.section-note {
    color: var(--ink-mid);
    font-size: .85rem;
    line-height: 1.65;
    max-width: 62ch;
    margin-bottom: 2rem;
}

/* ── Size table ─────────────────────────────────────────── */
.size-table-wrap {
    overflow-x: auto;
    border: 1px solid var(--line);
}
.size-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .85rem;
}
.size-table th {
    background: var(--ink);
    color: var(--paper);
    font-family: var(--font-mono);
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: .85rem 1.1rem;
    text-align: left;
    white-space: nowrap;
}
.size-table td {
    padding: .75rem 1.1rem;
    border-bottom: 1px solid var(--line);
    color: var(--ink-mid);
    white-space: nowrap;
}
.size-table tr:last-child td { border-bottom: none; }
.size-table tr:nth-child(even) td { background: var(--paper-2); }
.size-table tr:hover td { background: var(--clay-dim); color: var(--ink); }
.size-label {
    font-family: var(--font-display);
    font-size: 1rem;
    font-weight: 600;
    color: var(--ink);
}
.size-highlight {
    background: var(--clay-dim) !important;
}
.size-highlight .size-label { color: var(--clay); }

/* ── How to measure ─────────────────────────────────────── */
.measure-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}
.measure-card {
    border: 1px solid var(--line);
    padding: 1.5rem;
    background: var(--paper);
    transition: background .2s;
}
.measure-card:hover { background: var(--paper-2); }
.measure-icon {
    width: 40px; height: 40px;
    border: 1px solid var(--line);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: var(--clay);
    font-size: 1.1rem;
}
.measure-card h4 {
    font-family: var(--font-display);
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: .4rem;
}
.measure-card p {
    font-size: .82rem;
    color: var(--ink-mid);
    line-height: 1.65;
    margin: 0;
}

/* ── Fit tip banner ─────────────────────────────────────── */
.fit-tip {
    background: var(--ink);
    color: var(--paper);
    padding: 1.5rem 2rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-top: 2rem;
}
.fit-tip i { font-size: 1.25rem; color: var(--clay-dim); flex-shrink: 0; margin-top: .1rem; }
.fit-tip p { font-size: .85rem; line-height: 1.65; margin: 0; color: rgba(255,255,255,.8); }
.fit-tip strong { color: #fff; }

/* ── Responsive ─────────────────────────────────────────── */
@media (max-width: 640px) {
    .measure-grid { grid-template-columns: 1fr; }
    .unit-btn { padding: .5rem .85rem; }
}
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;1,500&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="catalogue">

    {{-- ── BREADCRUMB ──────────────────────────────────────── --}}
    <div class="breadcrumb-bar">
        <div class="wrap">
            <a href="{{ route('home') }}">Home</a>
            <i class="bi bi-chevron-right"></i>
            <span style="color:var(--ink);">Size Guide</span>
        </div>
    </div>

    {{-- ── PAGE HEADER ─────────────────────────────────────── --}}
    <div class="guide-head">
        <div class="wrap">
            <span class="guide-eyebrow">Fit & Sizing</span>
            <h1 class="guide-title">Size Guide</h1>
            <p class="guide-intro">
                All measurements are in centimetres unless toggled to inches.
                If you're between sizes, we recommend sizing up for a relaxed fit
                or sizing down for a more tailored look.
            </p>

            <div class="unit-toggle" role="group" aria-label="Unit selector">
                <button class="unit-btn active" id="btn-cm"  onclick="setUnit('cm')">cm</button>
                <button class="unit-btn"         id="btn-in"  onclick="setUnit('in')">inches</button>
            </div>
        </div>
    </div>

    {{-- ── HOW TO MEASURE ──────────────────────────────────── --}}
    <section class="guide-section">
        <div class="wrap">
            <span class="section-eyebrow">Step 1</span>
            <h2 class="section-title">How to Measure Yourself</h2>
            <p class="section-note">
                Use a soft measuring tape and stand naturally. Measure directly against
                your body, not over clothes.
            </p>

            <div class="measure-grid">
                <div class="measure-card">
                    <div class="measure-icon"><i class="bi bi-arrows-expand"></i></div>
                    <h4>Chest / Bust</h4>
                    <p>Wrap the tape around the fullest part of your chest, keeping it
                       horizontal and parallel to the floor. Keep the tape snug but not tight.</p>
                </div>
                <div class="measure-card">
                    <div class="measure-icon"><i class="bi bi-bezier2"></i></div>
                    <h4>Waist</h4>
                    <p>Measure around your natural waistline — the narrowest part of your
                       torso, usually about 2.5 cm above your navel. Exhale naturally first.</p>
                </div>
                <div class="measure-card">
                    <div class="measure-icon"><i class="bi bi-arrows-angle-expand"></i></div>
                    <h4>Hips</h4>
                    <p>Stand with your feet together. Measure around the fullest part of
                       your hips, usually about 20 cm below your natural waistline.</p>
                </div>
                <div class="measure-card">
                    <div class="measure-icon"><i class="bi bi-rulers"></i></div>
                    <h4>Inseam</h4>
                    <p>Measure from the top of your inner thigh down to the ankle bone.
                       Best done with a friend for accuracy.</p>
                </div>
                <div class="measure-card">
                    <div class="measure-icon"><i class="bi bi-arrow-down-up"></i></div>
                    <h4>Shoulder Width</h4>
                    <p>Measure across your back from the edge of one shoulder to the other,
                       following the natural curve. Keep the tape flat.</p>
                </div>
                <div class="measure-card">
                    <div class="measure-icon"><i class="bi bi-person-standing"></i></div>
                    <h4>Height</h4>
                    <p>Stand barefoot against a wall, heels together. Mark the top of your
                       head and measure from the floor to the mark.</p>
                </div>
            </div>

            <div class="fit-tip">
                <i class="bi bi-lightbulb"></i>
                <p><strong>Tip:</strong> Always use the largest measurement if two
                   measurements fall into different size brackets — for example, if your
                   chest is a Medium but your waist is a Large, choose Large.</p>
            </div>
        </div>
    </section>

    {{-- ── WOMEN'S SIZES ───────────────────────────────────── --}}
    <section class="guide-section">
        <div class="wrap">
            <span class="section-eyebrow">Women's Clothing</span>
            <h2 class="section-title">Women's Size Chart</h2>
            <p class="section-note">
                Our women's clothing follows international sizing. Nigerian women typically
                find our sizes run true to standard UK sizing.
            </p>

            <div class="size-table-wrap">
                <table class="size-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>UK</th>
                            <th>EU</th>
                            <th>US</th>
                            <th>Chest <span class="unit-label">cm</span></th>
                            <th>Waist <span class="unit-label">cm</span></th>
                            <th>Hips <span class="unit-label">cm</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="size-label">XS</span></td>
                            <td>6</td><td>34</td><td>2</td>
                            <td class="measure" data-cm="80–83">80–83</td>
                            <td class="measure" data-cm="61–64">61–64</td>
                            <td class="measure" data-cm="86–89">86–89</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">S</span></td>
                            <td>8</td><td>36</td><td>4</td>
                            <td class="measure" data-cm="84–87">84–87</td>
                            <td class="measure" data-cm="65–68">65–68</td>
                            <td class="measure" data-cm="90–93">90–93</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">M</span></td>
                            <td>10–12</td><td>38–40</td><td>6–8</td>
                            <td class="measure" data-cm="88–95">88–95</td>
                            <td class="measure" data-cm="69–76">69–76</td>
                            <td class="measure" data-cm="94–101">94–101</td>
                        </tr>
                        <tr class="size-highlight">
                            <td><span class="size-label">L</span></td>
                            <td>14–16</td><td>42–44</td><td>10–12</td>
                            <td class="measure" data-cm="96–103">96–103</td>
                            <td class="measure" data-cm="77–84">77–84</td>
                            <td class="measure" data-cm="102–109">102–109</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">XL</span></td>
                            <td>18–20</td><td>46–48</td><td>14–16</td>
                            <td class="measure" data-cm="104–111">104–111</td>
                            <td class="measure" data-cm="85–92">85–92</td>
                            <td class="measure" data-cm="110–117">110–117</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">XXL</span></td>
                            <td>22–24</td><td>50–52</td><td>18–20</td>
                            <td class="measure" data-cm="112–119">112–119</td>
                            <td class="measure" data-cm="93–100">93–100</td>
                            <td class="measure" data-cm="118–125">118–125</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ── MEN'S SIZES ─────────────────────────────────────── --}}
    <section class="guide-section">
        <div class="wrap">
            <span class="section-eyebrow">Men's Clothing</span>
            <h2 class="section-title">Men's Size Chart</h2>
            <p class="section-note">
                Men's sizes are measured at chest, waist and hip. Shirt collar and
                sleeve length are listed separately below.
            </p>

            <div class="size-table-wrap">
                <table class="size-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>UK / EU</th>
                            <th>US</th>
                            <th>Chest <span class="unit-label">cm</span></th>
                            <th>Waist <span class="unit-label">cm</span></th>
                            <th>Hips <span class="unit-label">cm</span></th>
                            <th>Shoulder <span class="unit-label">cm</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="size-label">XS</span></td>
                            <td>44</td><td>34</td>
                            <td class="measure" data-cm="84–88">84–88</td>
                            <td class="measure" data-cm="70–74">70–74</td>
                            <td class="measure" data-cm="86–90">86–90</td>
                            <td class="measure" data-cm="41–42">41–42</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">S</span></td>
                            <td>46</td><td>36</td>
                            <td class="measure" data-cm="89–93">89–93</td>
                            <td class="measure" data-cm="75–79">75–79</td>
                            <td class="measure" data-cm="91–95">91–95</td>
                            <td class="measure" data-cm="43–44">43–44</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">M</span></td>
                            <td>48–50</td><td>38–40</td>
                            <td class="measure" data-cm="94–98">94–98</td>
                            <td class="measure" data-cm="80–84">80–84</td>
                            <td class="measure" data-cm="96–100">96–100</td>
                            <td class="measure" data-cm="45–46">45–46</td>
                        </tr>
                        <tr class="size-highlight">
                            <td><span class="size-label">L</span></td>
                            <td>52–54</td><td>42–44</td>
                            <td class="measure" data-cm="99–104">99–104</td>
                            <td class="measure" data-cm="85–90">85–90</td>
                            <td class="measure" data-cm="101–106">101–106</td>
                            <td class="measure" data-cm="47–48">47–48</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">XL</span></td>
                            <td>56–58</td><td>46–48</td>
                            <td class="measure" data-cm="105–110">105–110</td>
                            <td class="measure" data-cm="91–96">91–96</td>
                            <td class="measure" data-cm="107–112">107–112</td>
                            <td class="measure" data-cm="49–50">49–50</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">XXL</span></td>
                            <td>60–62</td><td>50–52</td>
                            <td class="measure" data-cm="111–117">111–117</td>
                            <td class="measure" data-cm="97–103">97–103</td>
                            <td class="measure" data-cm="113–119">113–119</td>
                            <td class="measure" data-cm="51–53">51–53</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Shirt collar / sleeve --}}
            <h3 style="font-family:var(--font-display);font-size:1.25rem;font-weight:600;
                        margin:2.5rem 0 1rem;">Shirt Collar &amp; Sleeve</h3>
            <div class="size-table-wrap">
                <table class="size-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Collar <span class="unit-label">cm</span></th>
                            <th>Sleeve <span class="unit-label">cm</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="size-label">S</span></td>
                            <td class="measure" data-cm="37–38">37–38</td>
                            <td class="measure" data-cm="82–84">82–84</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">M</span></td>
                            <td class="measure" data-cm="39–40">39–40</td>
                            <td class="measure" data-cm="85–87">85–87</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">L</span></td>
                            <td class="measure" data-cm="41–42">41–42</td>
                            <td class="measure" data-cm="88–90">88–90</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">XL</span></td>
                            <td class="measure" data-cm="43–44">43–44</td>
                            <td class="measure" data-cm="91–93">91–93</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ── KIDS' SIZES ─────────────────────────────────────── --}}
    <section class="guide-section">
        <div class="wrap">
            <span class="section-eyebrow">Children's Clothing</span>
            <h2 class="section-title">Kids' Size Chart</h2>
            <p class="section-note">
                Kids' sizes are based on height and age as a guide only — children
                vary significantly. Always check the chest and waist measurements.
            </p>

            <div class="size-table-wrap">
                <table class="size-table">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>Age (guide)</th>
                            <th>Height <span class="unit-label">cm</span></th>
                            <th>Chest <span class="unit-label">cm</span></th>
                            <th>Waist <span class="unit-label">cm</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="size-label">2Y</span></td>
                            <td>1–2 yrs</td>
                            <td class="measure" data-cm="86–92">86–92</td>
                            <td class="measure" data-cm="52–53">52–53</td>
                            <td class="measure" data-cm="50–51">50–51</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">4Y</span></td>
                            <td>3–4 yrs</td>
                            <td class="measure" data-cm="98–104">98–104</td>
                            <td class="measure" data-cm="54–56">54–56</td>
                            <td class="measure" data-cm="52–53">52–53</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">6Y</span></td>
                            <td>5–6 yrs</td>
                            <td class="measure" data-cm="110–116">110–116</td>
                            <td class="measure" data-cm="58–60">58–60</td>
                            <td class="measure" data-cm="54–55">54–55</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">8Y</span></td>
                            <td>7–8 yrs</td>
                            <td class="measure" data-cm="122–128">122–128</td>
                            <td class="measure" data-cm="62–64">62–64</td>
                            <td class="measure" data-cm="57–58">57–58</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">10Y</span></td>
                            <td>9–10 yrs</td>
                            <td class="measure" data-cm="134–140">134–140</td>
                            <td class="measure" data-cm="66–69">66–69</td>
                            <td class="measure" data-cm="60–62">60–62</td>
                        </tr>
                        <tr>
                            <td><span class="size-label">12Y</span></td>
                            <td>11–12 yrs</td>
                            <td class="measure" data-cm="146–152">146–152</td>
                            <td class="measure" data-cm="72–76">72–76</td>
                            <td class="measure" data-cm="63–66">63–66</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ── FOOTWEAR ─────────────────────────────────────────── --}}
    <section class="guide-section">
        <div class="wrap">
            <span class="section-eyebrow">Footwear</span>
            <h2 class="section-title">Shoe Size Chart</h2>
            <p class="section-note">
                Measure your foot length while standing on a flat surface. Place
                your heel against a wall and mark the tip of your longest toe.
                Measure the distance in centimetres.
            </p>

            <div class="size-table-wrap">
                <table class="size-table">
                    <thead>
                        <tr>
                            <th>UK</th>
                            <th>EU</th>
                            <th>US (Men)</th>
                            <th>US (Women)</th>
                            <th>Foot Length <span class="unit-label">cm</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>3</td><td>36</td><td>4</td><td>5.5</td><td class="measure" data-cm="22.5">22.5</td></tr>
                        <tr><td>4</td><td>37</td><td>5</td><td>6.5</td><td class="measure" data-cm="23.5">23.5</td></tr>
                        <tr><td>5</td><td>38</td><td>6</td><td>7.5</td><td class="measure" data-cm="24.0">24.0</td></tr>
                        <tr><td>6</td><td>39</td><td>7</td><td>8.5</td><td class="measure" data-cm="24.8">24.8</td></tr>
                        <tr><td>7</td><td>40–41</td><td>8</td><td>9.5</td><td class="measure" data-cm="25.7">25.7</td></tr>
                        <tr><td>8</td><td>42</td><td>9</td><td>10.5</td><td class="measure" data-cm="26.5">26.5</td></tr>
                        <tr><td>9</td><td>43</td><td>10</td><td>11.5</td><td class="measure" data-cm="27.3">27.3</td></tr>
                        <tr><td>10</td><td>44</td><td>11</td><td>12.5</td><td class="measure" data-cm="28.0">28.0</td></tr>
                        <tr><td>11</td><td>45</td><td>12</td><td>13</td><td class="measure" data-cm="28.8">28.8</td></tr>
                        <tr><td>12</td><td>46</td><td>13</td><td>—</td><td class="measure" data-cm="29.6">29.6</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ── FAQ ─────────────────────────────────────────────── --}}
    <section class="guide-section" style="border-bottom:none;">
        <div class="wrap">
            <span class="section-eyebrow">Common Questions</span>
            <h2 class="section-title">Sizing FAQs</h2>

           <div style="max-width:68ch;margin-top:1.5rem;">
    @php
    $faqs = [
        [
            'q' => 'What if I\'m between sizes?',
            'a' => 'If you are between sizes, size up for a relaxed or oversized fit, or size down for a more fitted silhouette. When in doubt, check the model notes on each product page — we list the model\'s measurements and which size they are wearing.',
        ],
        [
            'q' => 'Do your sizes run large or small?',
            'a' => 'Our sizing runs true to standard UK sizing. However, individual garments may vary. We recommend checking the specific product\'s size notes before ordering.',
        ],
        [
            'q' => 'Can I exchange for a different size?',
            'a' => 'Yes. We accept size exchanges within 14 days of delivery, provided the item is unworn and in its original condition with tags attached. Contact our support team to arrange an exchange.',
        ],
        [
            'q' => 'How are plus sizes handled?',
            'a' => 'Products marked as plus size follow our XXL and above measurements. Check each product\'s size chart as extended sizes may differ slightly from the standard chart above.',
        ],
    ];
    @endphp

    @foreach($faqs as $i => $faq)
        <div style="border-top:1px solid var(--line);padding:1.25rem 0;">
            <button
                type="button"
                onclick="toggleFaq({{ $i }})"
                style="background:none;border:none;padding:0;width:100%;text-align:left;
                       display:flex;justify-content:space-between;align-items:center;
                       cursor:pointer;gap:1rem;"
                aria-expanded="false"
                id="faq-btn-{{ $i }}"
            >
                <span style="font-family:var(--font-display);font-size:1rem;
                             font-weight:500;color:var(--ink);">{{ $faq['q'] }}</span>
                <i class="bi bi-plus" id="faq-icon-{{ $i }}"
                   style="color:var(--clay);flex-shrink:0;font-size:1.2rem;"></i>
            </button>
            <div id="faq-body-{{ $i }}"
                 style="display:none;padding-top:.75rem;
                        font-size:.88rem;color:var(--ink-mid);line-height:1.7;">
                {{ $faq['a'] }}
            </div>
        </div>
    @endforeach
    <div style="border-top:1px solid var(--line);"></div>
</div>

            {{-- Still unsure CTA --}}
            <div style="margin-top:3rem;padding:2rem;border:1px solid var(--line);
                        display:flex;align-items:center;justify-content:space-between;
                        flex-wrap:wrap;gap:1.5rem;background:var(--paper-2);">
                <div>
                    <p style="font-family:var(--font-display);font-size:1.1rem;
                               font-weight:600;margin:0 0 .3rem;">Still unsure about your size?</p>
                    <p style="font-size:.85rem;color:var(--ink-mid);margin:0;">
                        Our team is happy to help you find the right fit.
                    </p>
                </div>
                <a href="{{ route('home') }}"
                   style="background:var(--ink);color:var(--paper);padding:.85rem 1.75rem;
                          font-family:var(--font-mono);font-size:.75rem;font-weight:700;
                          letter-spacing:.08em;text-transform:uppercase;
                          transition:background .2s;white-space:nowrap;"
                   onmouseover="this.style.background='var(--clay)'"
                   onmouseout="this.style.background='var(--ink)'">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

</div>

<script>
/* ── Unit toggle: cm ↔ inches ────────────────────────────── */
var currentUnit = 'cm';

function cmToIn(str) {
    // Handles ranges like "80–83" and single values like "24.5"
    return str.replace(/([\d.]+)/g, function (n) {
        return (parseFloat(n) / 2.54).toFixed(1);
    });
}

function setUnit(unit) {
    currentUnit = unit;

    document.querySelectorAll('.unit-label').forEach(function (el) {
        el.textContent = unit;
    });

    document.querySelectorAll('.measure').forEach(function (cell) {
        var cm = cell.getAttribute('data-cm');
        cell.textContent = unit === 'cm' ? cm : cmToIn(cm);
    });

    document.getElementById('btn-cm').classList.toggle('active', unit === 'cm');
    document.getElementById('btn-in').classList.toggle('active', unit === 'in');
}

/* ── FAQ accordion ───────────────────────────────────────── */
function toggleFaq(i) {
    var body = document.getElementById('faq-body-' + i);
    var icon = document.getElementById('faq-icon-' + i);
    var btn  = document.getElementById('faq-btn-' + i);
    var open = body.style.display === 'none';

    body.style.display = open ? 'block' : 'none';
    icon.className     = open ? 'bi bi-dash' : 'bi bi-plus';
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    icon.style.color   = open ? 'var(--ink)' : 'var(--clay)';
}
</script>

@endsection