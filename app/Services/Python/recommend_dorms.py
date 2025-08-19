# recommend.py

import sys
import json

# Sample data from database (in production, dapat gikan sa actual DB or via Laravel)
rooms = [
    {"roomID": 1, "price": 2500, "location": "Cebu City", "type": "single"},
    {"roomID": 2, "price": 1500, "location": "Lapu-Lapu", "type": "shared"},
    {"roomID": 3, "price": 1800, "location": "Mandaue", "type": "shared"},
    {"roomID": 4, "price": 3500, "location": "Cebu City", "type": "studio"}
]

# Read user question from stdin
input_data = json.loads(sys.stdin.read())
question = input_data.get("question", "").lower()

# Simple logic (this can be replaced with real ML/NLP)
recommendations = []
if "barato" in question or "cheap" in question:
    recommendations = sorted(rooms, key=lambda x: x["price"])[:3]
elif "cebu" in question:
    recommendations = [room for room in rooms if "cebu" in room["location"].lower()]
else:
    recommendations = rooms[:2]  # default

print(json.dumps(recommendations))
