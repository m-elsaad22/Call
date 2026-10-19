#!/usr/bin/env python3
"""Merge automated audit with refined off-topic rules."""
import csv
import json
from pathlib import Path

OUT = Path("/workspace/audit/output")
base = list(csv.DictReader((OUT / "article_audit_full.csv").open(encoding="utf-8-sig")))
refined = {
    int(r["id"]): r
    for r in csv.DictReader((OUT / "off_topic_refined.csv").open(encoding="utf-8-sig"))
}
dup_clusters = list(
    csv.DictReader((OUT / "duplicate_template_clusters.csv").open(encoding="utf-8-sig"))
)
id_to_tpl = {}
for c in dup_clusters:
    for pid in c["post_ids"].split("|"):
        if pid:
            id_to_tpl[int(pid)] = c["cluster_id"]

fields = list(base[0].keys()) + ["refined_review_flag", "refined_review_reason", "template_cluster"]
rows = []
for r in base:
    pid = int(r["post_id"])
    ref = refined.get(pid)
    r2 = dict(r)
    r2["refined_review_flag"] = "نعم" if ref else "لا"
    r2["refined_review_reason"] = ref["reason"] if ref else ""
    r2["template_cluster"] = id_to_tpl.get(pid, "")
    # downgrade false automated off-topic when refined says no and compat was wrong
    if not ref and r["compatibility"].startswith("غير"):
        r2["suggested_action"] = "يحتاج بيانات إضافية: مراجعة بشرية (تنبيه آلي محتمل)"
        r2["confidence"] = "منخفض"
    rows.append(r2)

with (OUT / "article_audit_refined.csv").open("w", newline="", encoding="utf-8-sig") as f:
    w = csv.DictWriter(f, fieldnames=fields)
    w.writeheader()
    w.writerows(rows)

summary = {
    "refined_review_rows": sum(1 for r in rows if r["refined_review_flag"] == "نعم"),
    "likely_false_auto_offtopic": sum(
        1 for r in rows if r["confidence"] == "منخفض" and r["compatibility"].startswith("غير")
    ),
}
(OUT / "refined_summary.json").write_text(json.dumps(summary, ensure_ascii=False, indent=2))
print(summary)
