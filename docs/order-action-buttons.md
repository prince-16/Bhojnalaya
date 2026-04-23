# Order Action Buttons

This document defines how order action buttons behave in the POS order screen.

## Buttons and Behavior

### Save
- Action key: `save`
- Order status saved: `open` (or `paid` if "It's Paid" is checked)
- Table status update: `running` (or `paid` if order status is `paid`)
- Client-side follow-up: none
- UI feedback: `Order saved successfully`

### Save & Print
- Action key: `save_and_print`
- Order status saved: `billed` (or `paid` if "It's Paid" is checked)
- Table status update: `running` (or `paid` if order status is `paid`)
- Client-side follow-up: triggers browser print dialog
- UI feedback: `Order saved and print triggered`

### Save & eBill
- Action key: `save_and_ebill`
- Order status saved: `ebilled` (or `paid` if "It's Paid" is checked)
- Table status update: `running` (or `paid` if order status is `paid`)
- Client-side follow-up: alert message indicating eBill generation
- UI feedback: `Order saved and eBill generated`

### KOT
- Action key: `kot`
- Order status saved: `kot_sent` (or `paid` if "It's Paid" is checked)
- Table status update: `running` (or `paid` if order status is `paid`)
- Client-side follow-up: none
- UI feedback: `KOT sent to kitchen`

### KOT & Print
- Action key: `kot_and_print`
- Order status saved: `kot_printed` (or `paid` if "It's Paid" is checked)
- Table status update: `running` (or `paid` if order status is `paid`)
- Client-side follow-up: triggers browser print dialog
- UI feedback: `KOT sent to kitchen and print triggered`

## Notes
- All five buttons use the same create/update order API path.
- If an order already exists, it is updated; otherwise a new order is created.
- Every save action sends the full current cart as order items.
- `Save & Print` uses the compact 58 mm bill print layout.
- `KOT & Print` uses the compact 58 mm KOT print layout.
- See `docs/58mm-print-layout.md` for exact printed fields and examples.
- Current implementation uses a client-side placeholder for the eBill integration.
