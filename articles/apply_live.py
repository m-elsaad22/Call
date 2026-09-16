#!/usr/bin/env python3
"""Apply live: publish missing related articles, retarget Al Ain links, scrub pest 0541."""

from __future__ import annotations

import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from publish_missing_related_articles import main as publish_main
from remove_0541_from_pest import main as pest_main


def main() -> int:
    pub = publish_main()
    pest = pest_main()
    print("publish_exit", pub, "pest_exit", pest)
    if pub == 2 and pest == 2:
        return 2
    return 0 if pub in (0, 2) and pest in (0, 2) and not (pub == 1 or pest == 1) else 1


if __name__ == "__main__":
    raise SystemExit(main())
