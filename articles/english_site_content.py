#!/usr/bin/env python3
"""English UAE content for the bilingual rukn-eltatawer.com site."""

from __future__ import annotations

# WhatsApp-only. Never put this on tel: / Call buttons.
PHONE = "+971586634710"
PHONE_TEL = "+971586634710"
PHONE_LOCAL = "0586634710"
WA = "971586634710"
CALL = "+971524314370"
CALL_LOCAL = "0524314370"
BRAND = "Rukn Eltatawer"

CITIES = [
    {
        "slug": "dubai",
        "name": "Dubai",
        "areas": "Deira, Jumeirah, Dubai Marina, Business Bay, Al Barsha, Al Qusais, Arabian Ranches and Dubai Hills",
        "note": "high-rise towers, villa compounds, and DEWA inspection requirements",
        "leak_ar": 237,
        "insul_ar": 181,
        "leak_slug": "water-leak-detection-dubai",
        "insul_slug": "roof-insulation-dubai",
    },
    {
        "slug": "abu-dhabi",
        "name": "Abu Dhabi",
        "areas": "Khalifa City, Al Reem Island, Corniche, MBZ City, Al Shamkha and Yas Island",
        "note": "coastal humidity, villa roofs, and municipality standards",
        "leak_ar": 397,
        "insul_ar": 359,
        "leak_slug": "water-leak-detection-abu-dhabi",
        "insul_slug": "roof-insulation-abu-dhabi",
    },
    {
        "slug": "sharjah",
        "name": "Sharjah",
        "areas": "Al Nahda, Al Majaz, Muwaileh, University City and Al Khan",
        "note": "older building stock and mixed residential-commercial blocks",
        "leak_ar": 402,
        "insul_ar": 349,
        "leak_slug": "water-leak-detection-sharjah",
        "insul_slug": "roof-insulation-sharjah",
    },
    {
        "slug": "al-ain",
        "name": "Al Ain",
        "areas": "Al Jimi, Al Ain City Centre, Hili, Zakher and Al Maqam",
        "note": "hot inland climate and wide villa roofs",
        "leak_ar": 405,
        "insul_ar": 1214,
        "leak_slug": "water-leak-detection-al-ain",
        "insul_slug": "roof-insulation-al-ain",
    },
    {
        "slug": "ajman",
        "name": "Ajman",
        "areas": "Al Nuaimiya, Al Rashidiya, Al Jurf and Corniche Ajman",
        "note": "compact city layouts and fast emergency access",
        "leak_ar": 422,
        "insul_ar": 1109,
        "leak_slug": "water-leak-detection-ajman",
        "insul_slug": "roof-insulation-ajman",
    },
    {
        "slug": "ras-al-khaimah",
        "name": "Ras Al Khaimah",
        "areas": "Al Nakheel, Al Dhait, Julphar and Al Hamra",
        "note": "coastal moisture plus mountain-area temperature swings",
        "leak_ar": 425,
        "insul_ar": 1111,
        "leak_slug": "water-leak-detection-in-ras-al-khaimah",
        "insul_slug": "roof-insulation-ras-al-khaimah",
    },
    {
        "slug": "fujairah",
        "name": "Fujairah",
        "areas": "Fujairah City, Sakamkam, Al Faseel and Kalba road communities",
        "note": "east-coast humidity and industrial plus residential mix",
        "leak_ar": 427,
        "insul_ar": 769,
        "leak_slug": "water-leak-detection-fujairah",
        "insul_slug": "roof-insulation-fujairah",
    },
    {
        "slug": "umm-al-quwain",
        "name": "Umm Al Quwain",
        "areas": "UAQ City, Al Raas, Falaj Al Mualla and the marina area",
        "note": "low-rise homes and coastal groundwater pressure",
        "leak_ar": 430,
        "insul_ar": 1216,
        "leak_slug": "water-leak-detection-in-umm-al-quwain",
        "insul_slug": "roof-insulation-umm-al-quwain",
        "leak_existing_id": 8996,
    },
]


def cta(label: str) -> str:
    return (
        '<div class="rukn-service-phone" style="background:#0A1F4E;color:#fff;'
        'padding:16px 18px;border-radius:12px;margin:22px 0;text-align:center;">'
        f"<p style='margin:0;font-size:18px;'><strong>{label}</strong> — Call "
        f'<a href="tel:{CALL}" style="color:#fff;font-weight:700;">{CALL_LOCAL}</a>'
        " or WhatsApp "
        f'<a href="https://wa.me/{WA}" style="color:#fff;font-weight:700;" '
        f'target="_blank" rel="noopener">{PHONE_LOCAL}</a></p></div>'
    )


def leak_article(city: dict) -> dict:
    name = city["name"]
    slug = city["leak_slug"]
    title = f"Water Leak Detection Company in {name}"
    keyword = f"water leak detection {name}"
    excerpt = (
        f"Professional water leak detection in {name} without breaking tiles. "
        f"Thermal cameras, acoustic listening, a written report, and a documented warranty."
    )
    other = "".join(
        f'<li><a href="/en/{c["leak_slug"]}/">Leak detection in {c["name"]}</a></li>'
        for c in CITIES
        if c["slug"] != city["slug"]
    )
    html = f"""
<p>Hidden water leaks in {name} raise utility bills, damage concrete, and spread mould long before a stain appears. {BRAND} is a water leak detection company in {name} that locates the source with thermal imaging, acoustic listening, and pressure testing — usually without breaking walls or floors.</p>
{cta(f"Book leak detection in {name}")}
<h2>Why properties in {name} leak more than people expect</h2>
<p>The mix of {city["note"]} puts constant stress on pipes, bathrooms, roofs and tanks. Across {city["areas"]}, our technicians regularly find pin-hole pipe corrosion, failed waterproofing under tiles, tank-float faults, and AC drain leaks that look like mystery damp.</p>
<p>English-speaking residents searching for “water leak detection {name}”, “water leakage”, or “leak detection company near me” need a team that writes the report in English and shows the leak point before any opening starts.</p>
<ul>
<li>Rising water bills with no visible leak</li>
<li>Peeling paint, hollow tiles, or a musty smell</li>
<li>Damp ceilings under bathrooms or roof tanks</li>
<li>Low pressure in one zone of the villa or apartment</li>
<li>Bathroom water leak detection after a renovation</li>
</ul>
<h2>How a {name} leak detection visit works</h2>
<ol>
<li><strong>Free on-site assessment</strong> of the affected rooms, shaft, roof and tank.</li>
<li><strong>Non-destructive testing</strong> with thermal cameras, geophones and trace gas where needed.</li>
<li><strong>Photo report</strong> showing the leak point before any repair starts.</li>
<li><strong>Repair and retest</strong> with a written warranty on the approved work.</li>
</ol>
<p>Most apartments in {name} are diagnosed in 1–3 hours. Large villas and buildings take longer because we isolate circuits instead of opening every floor. Emergency teams operate 24/7 when water is active and spreading in {city["areas"]}.</p>
<h2>Water leak detection services in {name}</h2>
<ul>
<li>Hidden pipe leaks in walls and floors</li>
<li>Bathroom, kitchen and wet-area leaks</li>
<li>Roof, tank and ceiling leaks</li>
<li>Swimming pool and irrigation leaks</li>
<li>Thermal scanning for water leakage</li>
<li>Post-repair verification for insurance, the landlord or the building manager</li>
</ul>
<h2>Related English pages</h2>
<ul>
<li><a href="/en/water-leak-detection-company-uae/">Water leak detection company in the UAE</a></li>
<li><a href="/en/{city["insul_slug"]}/">Roof insulation in {name}</a></li>
{other}
</ul>
<h2>FAQs — water leak detection in {name}</h2>
<h3>Do you break tiles to find the leak?</h3>
<p>No. We pinpoint first. Opening happens only at the confirmed point, which keeps repair cost down in {name}.</p>
<h3>How much does leak detection cost in {name}?</h3>
<p>Inspection starts from a clear call-out rate after we know the property type. You get the price before we start — no hidden extras.</p>
<h3>Can you come today?</h3>
<p>Yes for emergencies in {name}. Message WhatsApp {PHONE_LOCAL} with your area and photos.</p>
<h3>Do you cover all areas of {name}?</h3>
<p>Yes. We cover {city["areas"]} and nearby communities the same day when slots are open.</p>
{cta(f"Call {BRAND} in {name}")}
""".strip()
    return {
        "title": title,
        "slug": slug,
        "excerpt": excerpt,
        "keyword": keyword,
        "html": html,
        "ar_id": city["leak_ar"],
        "existing_id": city.get("leak_existing_id"),
        "categories": [166, 2457, 2778],
        "type": "post",
    }


def insulation_article(city: dict) -> dict:
    name = city["name"]
    slug = city["insul_slug"]
    title = f"Roof Insulation Company in {name}"
    keyword = f"roof insulation {name}"
    excerpt = (
        f"Roof waterproofing and thermal insulation in {name}. "
        f"Heat reduction, leak protection, and a written warranty from {BRAND}."
    )
    other = "".join(
        f'<li><a href="/en/{c["insul_slug"]}/">Roof insulation in {c["name"]}</a></li>'
        for c in CITIES
        if c["slug"] != city["slug"]
    )
    html = f"""
<p>Uninsulated roofs in {name} overheat rooms, crack waterproofing, and push cooling bills up all summer. {BRAND} is a roof insulation company in {name} installing thermal and waterproof roof systems for villas, buildings and warehouses across {city["areas"]}.</p>
{cta(f"Book roof insulation in {name}")}
<h2>Roof insulation that matches {name} weather</h2>
<p>Because of {city["note"]}, we specify systems for UV, ponding water, and expansion. A paint-only coat that fails after one summer costs more than doing the build-up correctly once. English queries such as “roof insulation UAE”, “roof waterproofing {name}” and “roof leakage repair” should reach this page, not an Arabic doorway.</p>
<ul>
<li>Thermal insulation to cut indoor heat</li>
<li>Waterproof membranes for roofs, parapets and joints</li>
<li>Tank, bathroom and wet-area waterproofing</li>
<li>Repair of existing failed insulation</li>
<li>Roof leakage repair after the source is confirmed</li>
</ul>
<h2>Our {name} insulation process</h2>
<ol>
<li>Roof survey, moisture check and photos</li>
<li>Surface repair, priming and slope correction where needed</li>
<li>Approved membrane / coating / board system</li>
<li>Flood test or inspection and a written warranty</li>
</ol>
<p>Work is scheduled around occupancy. We protect interiors and leave a clean site in {city["areas"]}.</p>
<h2>Related English pages</h2>
<ul>
<li><a href="/en/roof-insulation-company-uae/">Roof insulation company in the UAE</a></li>
<li><a href="/en/{city["leak_slug"]}/">Water leak detection in {name}</a></li>
<li><a href="/en/waterproofing-company-dubai/">Waterproofing company in Dubai</a></li>
{other}
</ul>
<h2>FAQs — roof insulation in {name}</h2>
<h3>Will insulation stop roof leaks?</h3>
<p>Waterproofing stops water. Thermal layers cut heat. We install both when the roof in {name} needs it, instead of selling one product for every problem.</p>
<h3>How long does a villa roof take?</h3>
<p>Typical villa roofs in {name} take 2–5 days after prep, depending on size and repairs.</p>
<h3>Do you warranty the work?</h3>
<p>Yes. Approved insulation and waterproofing is handed over with a written warranty and maintenance notes.</p>
{cta(f"Get a roof survey in {name}")}
""".strip()
    return {
        "title": title,
        "slug": slug,
        "excerpt": excerpt,
        "keyword": keyword,
        "html": html,
        "ar_id": city["insul_ar"],
        "categories": [2681, 2341, 2457, 2778],
        "type": "post",
    }


def leak_hub() -> dict:
    links = "\n".join(
        f'<li><a href="/en/{c["leak_slug"]}/">Water leak detection in {c["name"]}</a></li>'
        for c in CITIES
    )
    html = f"""
<p>{BRAND} is a UAE water leak detection company using thermal cameras, acoustic listening and pressure tests to find hidden leaks without random demolition. We work across Dubai, Abu Dhabi, Sharjah, Al Ain, Ajman, Ras Al Khaimah, Fujairah and Umm Al Quwain.</p>
{cta("Book UAE leak detection")}
<h2>English-speaking leak detection for residents and facilities teams</h2>
<p>Search demand in the UAE for “water leak detection Dubai”, “water leakage detection company”, “leak detection Dubai” and “water leak detection” is growing because hidden leaks waste water and fail tenancy inspections. This hub is the English entry point to our city pages.</p>
<p>Reports, quotations and WhatsApp coordination are in English. Arabic city pages remain on the main domain for Arabic search.</p>
<h2>Choose your city</h2>
<ul>
{links}
</ul>
<h2>What you receive</h2>
<ul>
<li>Same-day or 24/7 emergency attendance</li>
<li>Photo report of the leak point</li>
<li>Repair option with a written warranty</li>
<li>Coverage in all seven emirates</li>
<li>Bathroom water leak detection and thermal scanning</li>
</ul>
<p>Related: <a href="/en/roof-insulation-company-uae/">roof insulation in the UAE</a> and <a href="/en/waterproofing-company-dubai/">waterproofing in Dubai</a>.</p>
{cta("Call or WhatsApp leak detection")}
""".strip()
    return {
        "title": "Water Leak Detection Company in the UAE",
        "slug": "water-leak-detection-company-uae",
        "excerpt": "Non-destructive water leak detection across all seven emirates. Reports, repair, and warranty from Rukn Eltatawer.",
        "keyword": "water leak detection UAE",
        "html": html,
        "ar_id": 10890,
        "categories": [166, 2457, 2778],
        "type": "post",
    }


def insulation_hub() -> dict:
    links = "\n".join(
        f'<li><a href="/en/{c["insul_slug"]}/">Roof insulation in {c["name"]}</a></li>'
        for c in CITIES
    )
    html = f"""
<p>{BRAND} installs roof insulation and waterproofing for villas, buildings and commercial roofs in every emirate. English-speaking clients searching for “roof insulation UAE”, “roof waterproofing Dubai” and “ceiling insulation” land here, then open the city page for local crews.</p>
{cta("Book a UAE roof survey")}
<h2>City pages</h2>
<ul>
{links}
</ul>
<h2>Related English services</h2>
<ul>
<li><a href="/en/waterproofing-company-dubai/">Waterproofing company in Dubai</a></li>
<li><a href="/en/roof-leakage-repair-uae/">Roof leakage repair in the UAE</a></li>
<li><a href="/en/water-leak-detection-company-uae/">Water leak detection in the UAE</a></li>
</ul>
{cta("Call roof insulation UAE")}
""".strip()
    return {
        "title": "Roof Insulation Company in the UAE",
        "slug": "roof-insulation-company-uae",
        "excerpt": "Thermal insulation and roof waterproofing across the UAE. Written warranty from Rukn Eltatawer.",
        "keyword": "roof insulation UAE",
        "html": html,
        "ar_id": 10891,
        "categories": [2681, 2341, 2457, 2778],
        "type": "post",
    }


def extra_articles() -> list[dict]:
    items: list[dict] = []
    items.append(
        {
            "title": "Waterproofing Company in Dubai",
            "slug": "waterproofing-company-dubai",
            "excerpt": "Roof, bathroom and tank waterproofing in Dubai with a written warranty.",
            "keyword": "waterproofing company Dubai",
            "ar_id": 6361,
            "categories": [2681, 2341, 2778, 2457],
            "type": "post",
            "html": f"""
<p>Searches for “Dubai waterproofing company”, “waterproofing companies in Dubai” and “roof waterproofing Dubai” should reach an English page. {BRAND} waterproofs roofs, bathrooms, tanks and wet areas in Marina, Jumeirah, Al Barsha and Business Bay.</p>
{cta("Book Dubai waterproofing")}
<h2>Waterproofing we install</h2>
<ul><li>Roof membranes and coatings</li><li>Bathroom and wet-area systems</li><li>Water tank lining</li><li>Leak-related waterproofing repairs</li></ul>
<p>Related: <a href="/en/roof-insulation-dubai/">roof insulation in Dubai</a> and <a href="/en/water-leak-detection-dubai/">leak detection in Dubai</a>.</p>
{cta("Call Dubai waterproofing")}
""".strip(),
        }
    )
    items.append(
        {
            "title": "Roof Leakage Repair in the UAE",
            "slug": "roof-leakage-repair-uae",
            "excerpt": "Find the leak, then repair roof waterproofing across the UAE with a written warranty.",
            "keyword": "roof leakage repair",
            "ar_id": None,
            "categories": [2681, 166, 2778, 2457],
            "type": "post",
            "html": f"""
<p>Roof leakage repair in the UAE fails when contractors coat the stain instead of finding the entry point. {BRAND} combines leak detection with roof waterproofing so the repair lasts through summer heat and winter rain.</p>
{cta("Book roof leakage repair")}
<p>We attend villas and buildings in Dubai, Abu Dhabi, Sharjah, Al Ain, Ajman, Ras Al Khaimah, Fujairah and Umm Al Quwain. Start with <a href="/en/water-leak-detection-company-uae/">leak detection</a> or go straight to <a href="/en/roof-insulation-company-uae/">roof insulation</a> when the roof system has already failed.</p>
{cta("Call roof repair UAE")}
""".strip(),
        }
    )
    pest_cities = [
        ("sharjah", "Sharjah", "Al Nahda, Al Majaz, Muwaileh and Al Khan", None),
        ("dubai", "Dubai", "Deira, Al Qusais, Marina and Jumeirah", None),
        ("abu-dhabi", "Abu Dhabi", "Khalifa City, MBZ, Reem and Corniche", None),
        ("ajman", "Ajman", "Al Nuaimiya, Al Rashidiya and Al Jurf", None),
        ("ras-al-khaimah", "Ras Al Khaimah", "Al Nakheel, Al Hamra and Julphar", None),
        ("umm-al-quwain", "Umm Al Quwain", "UAQ City, Al Raas and Falaj Al Mualla", None),
    ]
    for slug, name, areas, ar_id in pest_cities:
        items.append(
            {
                "title": f"Pest Control Company in {name}",
                "slug": f"pest-control-company-{slug}",
                "excerpt": f"Licensed pest control in {name} for homes and businesses. Safe treatments and follow-up from {BRAND}.",
                "keyword": f"pest control {name}",
                "ar_id": ar_id,
                "categories": [83, 2457],
                "type": "post",
                "html": f"""
<p>English searches for “pest control {name}” and “best pest control company in {name}” need a licensed local team, not a one-spray visit. {BRAND} treats cockroaches, ants, bed bugs, rodents and flying insects in {areas}.</p>
{cta(f"Book pest control in {name}")}
<h2>What we treat in {name}</h2>
<ul><li>Cockroaches and ants</li><li>Bed bugs</li><li>Rodents</li><li>Mosquitoes and flies</li></ul>
<p>Materials are selected for occupied homes. You get a treatment plan and a follow-up window instead of a single spray-and-go visit.</p>
{cta(f"WhatsApp {name} pest control")}
""".strip(),
            }
        )
    items.append(
        {
            "title": "Home Cleaning Services in Sharjah",
            "slug": "home-cleaning-services-sharjah",
            "excerpt": "Home and apartment cleaning in Sharjah — deep cleaning, hourly maids, and move-in cleaning.",
            "keyword": "home cleaning Sharjah",
            "ar_id": None,
            "categories": [251, 2457],
            "type": "post",
            "html": f"""
<p>English queries such as “home cleaning Sharjah”, “cleaning services Sharjah” and “cleaning company Sharjah” convert when the page is actually in English. {BRAND} provides deep cleaning, kitchen and bathroom detailing, and furnished-apartment turnaround in Sharjah and neighbouring emirates.</p>
{cta("Book cleaning in Sharjah")}
<h2>Cleaning packages</h2>
<ul><li>Deep home cleaning</li><li>Kitchen and bathroom detailing</li><li>Move-in / move-out cleaning</li><li>Hourly cleaning where required</li></ul>
{cta("Call Sharjah cleaning")}
""".strip(),
        }
    )
    items.append(
        {
            "title": "Cleaning Company in Dubai",
            "slug": "cleaning-company-dubai-en",
            "excerpt": "Home, villa and apartment cleaning in Dubai. Deep cleaning and move-in packages from Rukn Eltatawer.",
            "keyword": "cleaning company Dubai",
            "ar_id": None,
            "categories": [251, 2457],
            "type": "post",
            "html": f"""
<p>Searches for “cleaning company Dubai” and “cleaners Dubai” should not land on an Arabic-only article. {BRAND} offers deep home cleaning, villa cleaning and move-in cleaning across Marina, Jumeirah, Al Barsha, Business Bay and Deira.</p>
{cta("Book Dubai cleaning")}
<p>Related English pages: <a href="/en/home-cleaning-services-sharjah/">home cleaning in Sharjah</a> and <a href="/en/pest-control-company-dubai/">pest control in Dubai</a>.</p>
{cta("Call Dubai cleaning")}
""".strip(),
        }
    )
    return items


def homepage_html() -> dict:
    city_leak = "".join(
        f'<li><a href="/en/{c["leak_slug"]}/">{c["name"]}</a></li>' for c in CITIES
    )
    city_ins = "".join(
        f'<li><a href="/en/{c["insul_slug"]}/">{c["name"]}</a></li>' for c in CITIES
    )
    html = f"""
<div class="rukn-en-home" style="max-width:980px;margin:0 auto;padding:12px 8px 40px;line-height:1.75;direction:ltr;text-align:left;">
<p style="letter-spacing:.04em;text-transform:uppercase;font-size:13px;color:#2e9df7;font-weight:700;">UAE home services</p>
<h1 style="font-size:34px;line-height:1.25;margin:8px 0 16px;">Rukn Eltatawer — leak detection, roof insulation and home services in the UAE</h1>
<p>Certified teams for water leak detection, roof insulation, waterproofing, AC and appliance repair, cleaning and pest control across all seven emirates. Written warranty, photo reports, and English-speaking coordinators.</p>
{cta("Call or WhatsApp now")}
<h2>High-intent English services</h2>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;">
<article style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;"><h3>Water leak detection</h3><p>Find hidden leaks without breaking tiles. Thermal + acoustic testing.</p><p><a href="/en/water-leak-detection-company-uae/">Open UAE leak detection</a></p></article>
<article style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;"><h3>Roof insulation</h3><p>Thermal insulation and waterproofing for villas and buildings.</p><p><a href="/en/roof-insulation-company-uae/">Open UAE roof insulation</a></p></article>
<article style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;"><h3>Waterproofing Dubai</h3><p>Roofs, bathrooms and tanks with a documented handover.</p><p><a href="/en/waterproofing-company-dubai/">Open Dubai waterproofing</a></p></article>
</div>
<h2>Water leak detection by city</h2>
<ul>{city_leak}</ul>
<h2>Roof insulation by city</h2>
<ul>{city_ins}</ul>
<h2>Also in English</h2>
<ul>
<li><a href="/en/pest-control-company-sharjah/">Pest control in Sharjah</a></li>
<li><a href="/en/pest-control-company-dubai/">Pest control in Dubai</a></li>
<li><a href="/en/home-cleaning-services-sharjah/">Home cleaning in Sharjah</a></li>
<li><a href="/en/cleaning-company-dubai-en/">Cleaning company in Dubai</a></li>
<li><a href="/en/about-us/">About Rukn Eltatawer</a></li>
<li><a href="/en/contact/">Contact us</a></li>
</ul>
<h2>Why English-speaking clients choose us</h2>
<ul>
<li>Reports and quotations in English</li>
<li>Coverage in every emirate</li>
<li>Non-destructive leak detection first</li>
<li>Written warranty on approved work</li>
</ul>
{cta("Talk to a coordinator")}
</div>
""".strip()
    return {
        "title": "Rukn Eltatawer | Leak Detection, Roof Insulation & Home Services in the UAE",
        "slug": "english-home",
        "excerpt": "English home for Rukn Eltatawer in the UAE: water leak detection, roof insulation, waterproofing, cleaning and pest control.",
        "keyword": "water leak detection UAE",
        "html": html,
        "ar_id": None,
        "categories": [2457],
        "type": "post",
    }


def about_html() -> dict:
    return {
        "title": "About Rukn Eltatawer",
        "slug": "about-us",
        "excerpt": "UAE home-services contractor for leak detection, roof insulation, waterproofing, cleaning and pest control.",
        "keyword": "Rukn Eltatawer",
        "existing_id": 11298,
        "ar_id": 7460,
        "categories": [2457],
        "type": "post",
        "html": f"""
<p>{BRAND} is a UAE home-services contractor for leak detection, roof insulation, waterproofing, maintenance, cleaning and pest control. Crews work in all seven emirates with English-speaking coordinators for residents, landlords and facilities teams.</p>
{cta("Contact Rukn Eltatawer")}
<h2>What we do</h2>
<ul>
<li>Water leak detection without random demolition</li>
<li>Roof thermal insulation and waterproofing</li>
<li>AC, electrical and general maintenance</li>
<li>Cleaning and licensed pest control</li>
</ul>
<p>Arabic site: <a href="https://www.rukn-eltatawer.com/">rukn-eltatawer.com</a>. English site starts at <a href="/en/english-home/">/en/</a>.</p>
""".strip(),
    }


def contact_html() -> dict:
    return {
        "title": "Contact Rukn Eltatawer",
        "slug": "contact",
        "excerpt": "Call or WhatsApp Rukn Eltatawer for leak detection, roof insulation and home services in the UAE.",
        "keyword": "contact Rukn Eltatawer",
        "ar_id": 7461,
        "categories": [2457],
        "type": "post",
        "html": f"""
<p>Call or WhatsApp {BRAND} for leak detection, roof insulation, waterproofing, cleaning and pest control anywhere in the UAE.</p>
{cta("Call now")}
<ul>
<li>Call: <a href="tel:{CALL}">{CALL_LOCAL}</a></li>
<li>WhatsApp: <a href="https://wa.me/{WA}">{PHONE_LOCAL}</a></li>
<li>Office: A 306, Mazid, MBZ, Abu Dhabi, UAE</li>
</ul>
<p>Send the emirate, property type, and photos. We reply with attendance time and a clear price before work starts.</p>
""".strip(),
    }


def all_content() -> list[dict]:
    pages = [homepage_html(), about_html(), contact_html()]
    posts = [leak_hub(), insulation_hub()]
    for city in CITIES:
        posts.append(leak_article(city))
        posts.append(insulation_article(city))
    posts.extend(extra_articles())
    return pages + posts
