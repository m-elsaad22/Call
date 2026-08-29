#!/usr/bin/env python3
"""English articles that follow the Arabic city-page outline for SEO."""

from __future__ import annotations

# WhatsApp-only. Never use on tel: / Call / FAB Call buttons.
PHONE = "+971586634710"
PHONE_LOCAL = "0586634710"
WA = "971586634710"
# Voice Call number (same as Arabic leak/insulation city pages).
CALL = "+971524314370"
CALL_LOCAL = "0524314370"
BRAND = "Rukn Eltatawer"

CITIES = {
    "dubai": {
        "name": "Dubai",
        "areas": "Deira, Jumeirah, Al Barsha, Al Qusais, Business Bay, Dubai Marina, Arabian Ranches and Dubai Hills",
        "note": "high network pressure, coastal humidity and extreme roof temperatures",
        "leak_ar": 237,
        "insul_ar": 181,
        "leak_slug": "water-leak-detection-dubai",
        "insul_slug": "roof-insulation-dubai",
    },
    "abu-dhabi": {
        "name": "Abu Dhabi",
        "areas": "Khalifa City, Al Reem Island, Corniche, MBZ City, Al Shamkha and Yas Island",
        "note": "coastal humidity, villa roofs and municipality inspection standards",
        "leak_ar": 397,
        "insul_ar": 359,
        "leak_slug": "water-leak-detection-abu-dhabi",
        "insul_slug": "roof-insulation-abu-dhabi",
    },
    "sharjah": {
        "name": "Sharjah",
        "areas": "Al Nahda, Al Majaz, Muwaileh, University City and Al Khan",
        "note": "older building stock and mixed residential-commercial blocks",
        "leak_ar": 402,
        "insul_ar": 349,
        "leak_slug": "water-leak-detection-sharjah",
        "insul_slug": "roof-insulation-sharjah",
    },
    "al-ain": {
        "name": "Al Ain",
        "areas": "Al Jimi, Hili, Zakher, Al Maqam and Al Ain city centre",
        "note": "hot inland climate and wide villa roofs",
        "leak_ar": 405,
        "insul_ar": 1214,
        "leak_slug": "water-leak-detection-al-ain",
        "insul_slug": "roof-insulation-al-ain",
    },
    "ajman": {
        "name": "Ajman",
        "areas": "Al Nuaimiya, Al Rashidiya, Al Jurf and Corniche Ajman",
        "note": "compact city layouts and fast emergency access",
        "leak_ar": 422,
        "insul_ar": 1109,
        "leak_slug": "water-leak-detection-ajman",
        "insul_slug": "roof-insulation-ajman",
    },
    "ras-al-khaimah": {
        "name": "Ras Al Khaimah",
        "areas": "Al Nakheel, Al Dhait, Julphar and Al Hamra",
        "note": "coastal moisture plus mountain-area temperature swings",
        "leak_ar": 425,
        "insul_ar": 1111,
        "leak_slug": "water-leak-detection-in-ras-al-khaimah",
        "insul_slug": "roof-insulation-ras-al-khaimah",
    },
    "fujairah": {
        "name": "Fujairah",
        "areas": "Fujairah City, Sakamkam, Al Faseel and Kalba road communities",
        "note": "east-coast humidity and mixed industrial-residential stock",
        "leak_ar": 427,
        "insul_ar": 769,
        "leak_slug": "water-leak-detection-fujairah",
        "insul_slug": "roof-insulation-fujairah",
    },
    "umm-al-quwain": {
        "name": "Umm Al Quwain",
        "areas": "UAQ City, Al Raas, Falaj Al Mualla and the marina",
        "note": "low-rise homes and coastal groundwater pressure",
        "leak_ar": 430,
        "insul_ar": 1216,
        "leak_slug": "water-leak-detection-in-umm-al-quwain",
        "insul_slug": "roof-insulation-umm-al-quwain",
        "leak_existing_id": 8996,
    },
}


def author_box(city: str) -> str:
    return (
        '<div style="background:#f9f9f9;border:1px solid #ddd;padding:15px;margin:20px 0;'
        'border-radius:8px;font-size:14px;">'
        '<i class="fa-solid fa-pen-nib"></i> <strong>Written by:</strong> '
        "Eng. Samer Al-Abdullah — licensed plumbing and water-systems engineer, "
        f"13 years in {city} and the UAE &nbsp;|&nbsp; "
        '<i class="fa-solid fa-calendar-days"></i> <strong>Last update:</strong> May 2026 &nbsp;|&nbsp; '
        '<i class="fa-solid fa-circle-check"></i> <strong>Reviewed by:</strong> '
        f"{BRAND} technical quality team</div>"
    )


def leak_html(city: dict, img: str = "") -> str:
    n = city["name"]
    img_block = f"<p>{img}</p>" if img else ""
    return f"""
<h1>Water Leak Detection Company in {n} 2026 — non-destructive testing and a written warranty</h1>
{author_box(n)}
{img_block}
<section>
<h2>Water leak detection company in {n} 2026 — why every day of delay costs more</h2>
<div style="background:#f0f7ff;border-left:6px solid #0056b3;padding:30px;border-radius:15px;margin-bottom:30px;">
<h3>What is water leak detection in {n}?</h3>
<p>A <strong>water leak detection company in {n}</strong> locates leaks in pipes, tanks, bathrooms, walls and roofs with electronic tools — thermal cameras, geophones and pressure tests — instead of breaking tiles at random. {BRAND} has treated thousands of cases across {city["areas"]}.</p>
</div>
<p>Leaks in {n} are driven by {city["note"]}. Most stay silent inside slabs and walls for months. The professional response is pinpoint detection first, then a small opening at the confirmed point.</p>
[post_call]
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:15px;margin:20px 0;">
<div style="background:#fff;border:1px solid #e2e8f0;padding:15px;border-radius:10px;"><h3>Thermal cameras</h3><p>Find hidden moisture behind walls and ceilings before it becomes structural damage.</p></div>
<div style="background:#fff;border:1px solid #e2e8f0;padding:15px;border-radius:10px;"><h3>Acoustic geophones</h3><p>Listen to pressurised pipe noise through finishes without demolition.</p></div>
<div style="background:#fff;border:1px solid #e2e8f0;padding:15px;border-radius:10px;"><h3>Trace-gas / pressure tests</h3><p>Isolate circuits and confirm the leak point to centimetre accuracy.</p></div>
<div style="background:#fff;border:1px solid #e2e8f0;padding:15px;border-radius:10px;"><h3>Tank leak detection</h3><p>Roof tanks, ground tanks and float faults that look like “mystery damp”.</p></div>
<div style="background:#fff;border:1px solid #e2e8f0;padding:15px;border-radius:10px;"><h3>Repair without random breaking</h3><p>Open only at the marked point, then retest.</p></div>
<div style="background:#fff;border:1px solid #e2e8f0;padding:15px;border-radius:10px;"><h3>Written report and warranty</h3><p>Photos of the leak point plus a documented warranty on approved repair.</p></div>
</div>
</section>
<section>
<h2>8 signs your {n} property has a hidden water leak</h2>
<h3>A sudden rise in the water bill</h3>
<p>Unexplained DEWA-style bill jumps are the most common reason clients search for water leak detection in {n}.</p>
<h3>Damp stains and colour change on walls</h3>
<p>Brown rings, bubbling paint and hollow tiles usually mean moisture behind the finish.</p>
<h3>Mould smell and poorer indoor air</h3>
<p>Musty bathrooms and bedrooms after AC cycles often trace to a wet shaft or slab.</p>
<h3>The meter still moves when taps are closed</h3>
<p>A spinning meter with everything shut is a live leak until proven otherwise.</p>
<h3>Unexplained water sounds at night</h3>
<p>Hissing in walls after midnight is a classic pressurised-pipe leak.</p>
<h3>Swollen or cracked floors and tiles</h3>
<p>Hollow sounds underfoot in wet areas point to failed waterproofing or a pipe leak.</p>
<h3>Humidity insects</h3>
<p>Silverfish and similar pests follow chronic damp.</p>
<h3>Peeling paint and falling plaster</h3>
<p>Especially under bathrooms and roof tanks in {city["areas"]}.</p>
</section>
[post_call]
<section>
<h2>Root causes of water leaks in {n} buildings</h2>
<h3>Corrosion and ageing pipes</h3>
<p>Galvanised and mixed-metal joints fail early in {n} water chemistry.</p>
<h3>Thermal expansion</h3>
<p>Daily heat cycles stress concealed pipes in roofs and façades.</p>
<h3>Excess network pressure</h3>
<p>High-rise booster pumps and villa pumps both split weak fittings.</p>
<h3>Original installation errors</h3>
<p>Unclipped pipes, missing sleeves and poor welding show up years later.</p>
<h3>Structural movement and nearby excavation</h3>
<p>Settlement cracks become leak paths through wet areas.</p>
<h3>Water quality effects on pipes</h3>
<p>Scale and chlorides accelerate pin-holes.</p>
</section>
<section>
<h2>Leak detection equipment used in {n} — no breaking, centimetre accuracy</h2>
<p>We combine thermal imaging, geophones, gas pressure tests, acoustic correlators, dye tracing and pipe microphones. The tool follows the building, not a one-device sales pitch.</p>
[post_steps]
</section>
<section>
<h2>How {BRAND} runs leak detection in {n} — step by step</h2>
<h3>Step 1 — intake and facts</h3>
<p>Bills, photos, affected rooms, tank and AC drains.</p>
<h3>Step 2 — system pressure test</h3>
<p>Isolate circuits so we do not open the whole floor.</p>
<h3>Step 3 — full thermal scan</h3>
<p>Ceilings, shafts, bathrooms and the roof tank zone.</p>
<h3>Step 4 — acoustic listening</h3>
<p>Confirm the point through finishes.</p>
<h3>Step 5 — technical report and solution</h3>
<p>You see the leak location before repair starts.</p>
<h3>Step 6 — repair, retest and warranty</h3>
<p>Retest after the fix. Approved work is warranted in writing.</p>
[post_call]
</section>
<section>
<h2>Water leak detection services in {n} — pricing and details</h2>
<h3>Why clients in {n} choose {BRAND}</h3>
<ul>
<li>13+ years of leak detection in {n}</li>
<li>Non-destructive testing first</li>
<li>24/7 emergency attendance</li>
<li>Written technical report</li>
<li>Clear price before work — no surprise extras</li>
<li>Warranty on approved repair</li>
</ul>
<p>Inspection is quoted after we know the property type (apartment, villa, building). You approve the figure before we start.</p>
[post_features]
[post_prices]
[post_services]
</section>
<section>
<h2>The 10 questions {n} residents ask about water leak detection</h2>
<h3>How much does water leak detection cost in {n} in 2026?</h3>
<p>Call-out plus inspection is priced after property type. Repair is separate and quoted from the report.</p>
<h3>How long does detection take?</h3>
<p>Most apartments: 1–3 hours. Large villas and buildings take longer because we isolate zones.</p>
<h3>Can you really find a leak without breaking walls?</h3>
<p>Yes. Opening happens only at the confirmed point.</p>
<h3>Why is my water bill high with no visible leak?</h3>
<p>Hidden pipe, tank-float or irrigation leaks. Detection answers that without guesswork.</p>
<h3>Do you cover all of {n}?</h3>
<p>Yes — {city["areas"]} and nearby communities.</p>
<h3>Is repair warranted?</h3>
<p>Approved repair is handed over with a written warranty.</p>
<h3>Is a leak near electrical work dangerous?</h3>
<p>Yes. Treat it as urgent and isolate electrics if water is active.</p>
<h3>Are roof-tank leaks serious?</h3>
<p>They soak slabs and rooms below. We check tanks on every roof visit.</p>
<h3>When should I do preventive water-network checks in {n}?</h3>
<p>After a bill spike, before tenancy handover, or every 12–18 months on older villas.</p>
<h3>Thermal camera vs geophone — which is better?</h3>
<p>They answer different questions. We use both when the leak is concealed.</p>
</section>
<section>
<h2>How to choose a water leak detection company in {n}</h2>
<ol>
<li>Licence and insurance</li>
<li>Real detection equipment on site — not dye-only</li>
<li>Written warranty</li>
<li>Photo report after the visit</li>
<li>Local experience in {n}, not a one-call broker</li>
</ol>
</section>
<section>
<h2>Water leak detection across {n} areas</h2>
<p>Teams cover {city["areas"]}. Emergency crews operate 24/7 when water is spreading.</p>
[post_gallery]
[post_call]
</section>
<section>
<h2>Book {BRAND} — water leak detection company in {n}</h2>
<p>Call <a href="tel:{CALL}">{CALL_LOCAL}</a> or WhatsApp <a href="https://wa.me/{WA}">{PHONE_LOCAL}</a>. Send the area, property type and photos. You get attendance time and a clear price before work starts.</p>
<h3>Summary</h3>
<p>{BRAND} is a water leak detection company in {n} using thermal and acoustic testing without random demolition, with a written report and warranty on approved repair across {city["areas"]}.</p>
</section>
""".strip()


def insulation_html(city: dict, img: str = "") -> str:
    n = city["name"]
    img_block = f"<p>{img}</p>" if img else ""
    return f"""
<h1>Roof Insulation Company in {n} 2026 — heat and leak protection with a written warranty</h1>
{author_box(n)}
{img_block}
<section>
<h2>Roof insulation company in {n} 2026 — why the roof cannot wait</h2>
<div style="background:#f0f7ff;border-left:6px solid #0056b3;padding:30px;border-radius:15px;margin-bottom:30px;">
<h3>What is roof insulation in {n}?</h3>
<p>A <strong>roof insulation company in {n}</strong> installs thermal and waterproof systems so villas and buildings stay cooler and dry. {BRAND} specifies systems for {city["note"]} across {city["areas"]}.</p>
</div>
<ul>
<li>Spray-foam thermal insulation</li>
<li>Polymer waterproof membranes</li>
<li>Crack and leak repair before the new system</li>
<li>Tank and wet-area waterproofing</li>
<li>Written, certified warranty</li>
</ul>
[post_call]
</section>
<section>
<h2>8 signs your {n} roof needs insulation now</h2>
<h3>Electricity bills up 20–35%</h3>
<p>Uninsulated roofs dump heat into upper floors all summer in {n}.</p>
<h3>Damp spots and dark cracks</h3>
<p>Failed waterproofing shows on ceilings under the roof.</p>
<h3>Mould on ceilings</h3>
<p>Chronic roof moisture plus AC condensate.</p>
<h3>Severe heat on top floors</h3>
<p>Thermal insulation is the layer that cuts that load.</p>
<h3>Cracked walls and falling paint</h3>
<p>Expansion from a hot, uninsulated slab.</p>
<h3>Water through the ceiling</h3>
<p>That is waterproofing failure — not a paint problem.</p>
<h3>The system is 8–10 years old</h3>
<p>UV in {n} finishes cheap coatings in a few summers.</p>
<h3>Blisters in the old insulation</h3>
<p>Trapped vapour. We remove failed layers instead of coating over them.</p>
</section>
<section>
<h2>Why roof insulation fails in {n}</h2>
<h3>UV radiation</h3>
<h3>Coastal humidity and night condensation</h3>
<h3>Daily thermal shock</h3>
<h3>Poor first-time workmanship and weak materials</h3>
<p>A paint-only coat that fails after one summer costs more than a correct build-up once.</p>
</section>
[post_steps]
<section>
<h2>How {BRAND} insulates roofs in {n}</h2>
<ol>
<li>Free site survey and moisture check</li>
<li>Clean-down and removal of failed insulation</li>
<li>Primer</li>
<li>Waterproof layer</li>
<li>Thermal layer</li>
<li>UV protection</li>
<li>Flood test or inspection and written warranty</li>
</ol>
[post_call]
</section>
<section>
<h2>7 mistakes when hiring a roof insulation company in {n}</h2>
<ul>
<li>Taking the cheapest quote with no specification</li>
<li>No written warranty</li>
<li>Coating over dead insulation</li>
<li>Ignoring corners and drains</li>
<li>Waterproofing without thermal — or the reverse</li>
<li>A general contractor with no roof system</li>
<li>Skipping annual roof checks</li>
</ul>
</section>
<section>
<h2>Roof insulation services in {n} — prices and specifications</h2>
<ul>
<li>10+ years of roof work</li>
<li>Thousands of completed roofs</li>
<li>Internationally approved materials</li>
<li>Written warranty up to 10 years</li>
<li>Response within 24 hours</li>
<li>Transparent pricing</li>
</ul>
[post_features]
[post_prices]
[post_services]
</section>
<section>
<h2>FAQs — roof insulation company in {n}</h2>
<h3>How much does roof insulation cost in {n} in 2026?</h3>
<p>Price follows roof area, access, repairs and the system. You get the figure after survey.</p>
<h3>How long does roof insulation last in {n}?</h3>
<p>A specified system with UV protection lasts many summers; cheap coatings do not.</p>
<h3>Does thermal insulation really cut electricity bills?</h3>
<p>It cuts roof heat gain. Clients in {n} typically feel upper floors first.</p>
<h3>What is the best roof insulation type in {n}?</h3>
<p>The one that matches your deck, ponding and budget — we do not sell one product for every roof.</p>
<h3>How long does a villa roof take?</h3>
<p>Usually 2–5 days after prep, depending on repairs.</p>
<h3>Can you insulate in {n} summer?</h3>
<p>Yes, with the right materials and working hours.</p>
<h3>Why does my ceiling still leak if old insulation exists?</h3>
<p>The membrane has failed. We find the entry, then replace the system.</p>
<h3>Do you cover all of {n}?</h3>
<p>Yes — {city["areas"]}.</p>
<h3>Is there a warranty?</h3>
<p>Yes. Written warranty on approved insulation and waterproofing.</p>
<h3>Foam vs rigid boards in {n}?</h3>
<p>Foam suits complex roofs; boards suit clean, accessible decks. We specify on site.</p>
</section>
<section>
<h2>Book the roof insulation company in {n} — {BRAND}</h2>
<p>Call <a href="tel:{CALL}">{CALL_LOCAL}</a> or WhatsApp <a href="https://wa.me/{WA}">{PHONE_LOCAL}</a>.</p>
<h3>Summary</h3>
<p>{BRAND} is a roof insulation company in {n} installing thermal and waterproof roof systems with a written warranty across {city["areas"]}.</p>
[post_gallery]
[post_call]
</section>
""".strip()


def leak_hub_html() -> str:
    links = "\n".join(
        f'<li><a href="/en/{c["leak_slug"]}/">Water leak detection company in {c["name"]}</a></li>'
        for c in CITIES.values()
    )
    return f"""
<h1>Water Leak Detection in the UAE</h1>
<p>{BRAND} provides non-destructive water leak detection across Dubai, Abu Dhabi, Sharjah, Al Ain, Ajman, Ras Al Khaimah, Fujairah and Umm Al Quwain — the same service line as our Arabic leak-detection pages, written for English search.</p>
[post_call]
<h2>Choose your city</h2>
<ul>{links}</ul>
<p>Related: <a href="/en/roof-insulation-company-uae/">roof insulation in the UAE</a>.</p>
""".strip()


def insulation_hub_html() -> str:
    links = "\n".join(
        f'<li><a href="/en/{c["insul_slug"]}/">Roof insulation company in {c["name"]}</a></li>'
        for c in CITIES.values()
    )
    return f"""
<h1>Roof Insulation in the UAE</h1>
<p>{BRAND} installs roof thermal insulation and waterproofing in every emirate — matching our Arabic roof-insulation pages, for English queries such as roof insulation UAE.</p>
[post_call]
<h2>Choose your city</h2>
<ul>{links}</ul>
<p>Related: <a href="/en/waterproofing-company-dubai/">waterproofing in Dubai</a> and <a href="/en/water-leak-detection-company-uae/">water leak detection</a>.</p>
""".strip()


def about_html() -> str:
    return f"""
<div class="about-us-container" style="direction:ltr;text-align:left;font-size:18px;line-height:1.8;">
<p>Since we started, the aim has been clear: <strong>the client comes first</strong> — professional, reliable home services, done properly.</p>
<hr />
[post_services]
<ul>
<li>AC and cooling maintenance</li>
<li>Fast, practical solutions for home problems</li>
</ul>
<p>We want every client to feel safe dealing with us.</p>
<hr />
<h2>Our mission</h2>
<p>Complete home services with proper equipment and qualified crews, focused on:</p>
<ul>
<li>Speed and quality of execution</li>
<li>A clean, healthy site</li>
<li>Transparent prices — no hidden fees</li>
<li>Listening to feedback and improving</li>
</ul>
<hr />
<h2>Our values</h2>
<ul>
<li>Integrity and transparency</li>
<li>Professional workmanship</li>
<li>On-time attendance</li>
<li>Safe methods for people and the environment</li>
<li>Client satisfaction</li>
</ul>
<hr />
[post_features]
<hr />
<h2>Contact us</h2>
<p><strong>Email:</strong> support@rukn-eltatawer.com</p>
<p><strong>WhatsApp:</strong> <a href="https://wa.me/{WA}">{PHONE_LOCAL}</a></p>
<p><strong>Address:</strong> Office 306, Tower A, Mazid Mall, Mohamed Bin Zayed City, Abu Dhabi, UAE</p>
</div>
""".strip()


def contact_html() -> str:
    return f"""
<div class="contact-us-container" style="direction:ltr;text-align:left;font-size:18px;line-height:1.8;">
<p>{BRAND} is a group of companies for integrated home services — from cleaning and plumbing to electrical work, painting, pest control and moving — with UAE experience, guaranteed quality and competitive prices.</p>
<p>Book now. WhatsApp <a href="https://wa.me/{WA}">{PHONE_LOCAL}</a>.</p>
</div>
""".strip()


def waterproofing_html() -> str:
    return f"""
<div class="service-article">
<p>If you need a <strong>waterproofing company in Dubai</strong>, {BRAND} waterproofs roofs, bathrooms and tanks in Al Barsha, Jumeirah, Mirdif, Al Warqa, International City, Al Rashidiya, Al Quoz and Al Qusais.</p>
<h2>What you get</h2>
<ul>
<li>Accurate inspection with professional equipment</li>
<li>Long-term systems, not a one-coat patch</li>
<li>Written warranty</li>
</ul>
<h2>How we work</h2>
<ol>
<li>Book the visit</li>
<li>Survey with modern tools</li>
<li>Report and recommended system</li>
<li>Install or repair with warranty</li>
</ol>
<h2>Areas in Dubai</h2>
<p>Al Barsha, Jumeirah, Mirdif, Al Warqa, International City, Al Rashidiya, Al Quoz and Al Qusais.</p>
<h2>WhatsApp</h2>
<p><a href="https://wa.me/{WA}">WhatsApp {PHONE_LOCAL}</a></p>
</div>
""".strip()
