"""PHP serialize with UTF-8 byte lengths (WordPress maybe_serialize)."""


def php_serialize(value):
    if value is None:
        return "N;"
    if isinstance(value, bool):
        return "b:1;" if value else "b:0;"
    if isinstance(value, int):
        return f"i:{value};"
    if isinstance(value, str):
        n = len(value.encode("utf-8"))
        return f's:{n}:"{value}";'
    if isinstance(value, list):
        inner = "".join(php_serialize(i) + php_serialize(v) for i, v in enumerate(value))
        return f"a:{len(value)}:{{{inner}}}"
    if isinstance(value, dict):
        inner = "".join(php_serialize(k) + php_serialize(v) for k, v in value.items())
        return f"a:{len(value)}:{{{inner}}}"
    raise TypeError(type(value))


def faqs(pairs):
    data = {}
    for i, (q, a) in enumerate(pairs, 1):
        data[f"UaFaq{i:05d}"] = {"question": q, "answer": a}
    return data


def features(title, content, items):
    return {
        "features__title": title,
        "features__content": content,
        "yourcolor__post_features": [
            {"icon": "", "title": t, "content": c} for t, c in items
        ],
    }


def steps(title, content, items):
    return {
        "work_steps__title": title,
        "work_steps__content": content,
        "work_steps_items": [{"title": t, "content": c} for t, c in items],
    }


def services(title, content, items):
    return {
        "services__title": title,
        "services__content": content,
        "post_services_items": [
            {"title": t, "content": c, "image_id": ""} for t, c in items
        ],
    }
