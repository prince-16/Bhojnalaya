# 58mm Print Layout

This document defines the compact thermal printer output used for 58 mm paper.

## Goals

- Avoid printing the full browser page
- Keep layout readable on narrow paper
- Print only essential information
- Separate kitchen printing from bill printing

## KOT Print Contents

The KOT print is kitchen-facing and should contain only preparation-relevant information.

### Header

- Hotel/restaurant name
- Restaurant line/subtitle
- Small text logo/mark
- `KOT` title

### Meta Rows

- Date and time
- Order number
- Table number/name
- Floor name
- Order type

### Item Body

Each row prints:

- Item name
- Quantity
- Item note if present

### Footer

- Order-level note if present
- `Kitchen copy`

### KOT Example

```text
      Bp
  Bhojnalaya
   Restaurant
      KOT
------------------------------
Date      24/04/2026, 09:35 pm
Order     ORD-202604241234
Table     T4
Floor     Ground Floor
Type      Dine In
------------------------------
Paneer Tikka               2
------------------------------
Butter Naan                3
------------------------------
Dal Makhani                1
  Note: less spicy
------------------------------
Kitchen copy
```

## Bill Print Contents

The bill print is customer-facing and should contain billing amounts.

### Header

- Hotel/restaurant name
- Restaurant line/subtitle
- Small text logo/mark
- `Bill` title

### Meta Rows

- Date and time
- Order number
- Table number/name
- Floor name
- Order type

### Item Body

Each row prints:

- Item name
- Quantity x rate
- Line total
- Item note if present

### Totals

- Subtotal
- Tax
- Grand total

### Footer

- Order-level note if present
- `Thank you for visiting`

### Bill Example

```text
      Bp
  Bhojnalaya
   Restaurant
      BILL
------------------------------
Date      24/04/2026, 09:35 pm
Order     ORD-202604241234
Table     T4
Floor     Ground Floor
Type      Dine In
------------------------------
Paneer Tikka
2 x 220.00               440.00
------------------------------
Butter Naan
3 x 40.00                120.00
------------------------------
Dal Makhani
1 x 180.00               180.00
------------------------------
Subtotal                 740.00
Tax                        0.00
Grand Total              740.00
------------------------------
Thank you for visiting
```

## Current Implementation Notes

- `Save & Print` uses the 58 mm bill layout.
- `KOT & Print` uses the 58 mm KOT layout.
- `Save & eBill` does not print to paper; it currently shows a placeholder alert for digital bill flow.
- `KOT` changes order status without printing.

## Source Files

- `resources/js/print/thermalPrint.js`
- `resources/js/components/App.vue`
- `docs/order-action-buttons.md`