function escapeHtml(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;')
}

function formatDateTime(dateInput) {
  const date = dateInput instanceof Date ? dateInput : new Date(dateInput ?? Date.now())

  return new Intl.DateTimeFormat('en-IN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date)
}

function formatMoney(value) {
  return Number(value ?? 0).toFixed(2)
}

function openPrintWindow(title, content) {
  if (typeof window === 'undefined') {
    return
  }

  const printWindow = window.open('', '_blank', 'width=420,height=820')

  if (!printWindow) {
    window.alert('Unable to open print window. Please allow pop-ups for printing.')
    return
  }

  printWindow.document.open()
  printWindow.document.write(content)
  printWindow.document.close()
  printWindow.focus()
  printWindow.onload = () => {
    printWindow.print()
    printWindow.close()
  }
  printWindow.document.title = title
}

function buildMetaRows(rows) {
  return rows
    .filter((row) => row?.value)
    .map((row) => `<div class="meta-row"><span>${escapeHtml(row.label)}</span><strong>${escapeHtml(row.value)}</strong></div>`)
    .join('')
}

function buildShell({ restaurantName, restaurantLine, logoText, title, metaRows, body, footer }) {
  return `<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>${escapeHtml(title)}</title>
    <style>
      @page { size: 58mm auto; margin: 0; }
      * { box-sizing: border-box; }
      html, body { margin: 0; padding: 0; }
      body {
        width: 58mm;
        padding: 2mm;
        font-family: Consolas, 'Courier New', monospace;
        font-size: 11px;
        line-height: 1.35;
        color: #000;
        background: #fff;
      }
      .receipt { width: 100%; }
      .center { text-align: center; }
      .logo {
        width: 20px;
        height: 20px;
        margin: 0 auto 4px;
        border: 1px solid #000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
      }
      .brand { font-size: 14px; font-weight: 700; }
      .brand-line { font-size: 10px; margin-top: 1px; }
      .title { margin: 6px 0 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
      .divider { border-top: 1px dashed #000; margin: 6px 0; }
      .meta-row, .line-item, .total-row {
        display: flex;
        justify-content: space-between;
        gap: 8px;
      }
      .meta-row strong, .total-row strong { font-weight: 700; }
      .line-item { align-items: flex-start; }
      .line-name { flex: 1; min-width: 0; word-break: break-word; }
      .line-side { white-space: nowrap; text-align: right; }
      .note { margin-top: 2px; padding-left: 8px; font-size: 10px; }
      .totals { margin-top: 4px; }
      .grand-total { font-size: 12px; font-weight: 700; }
      .footer { margin-top: 6px; font-size: 10px; text-align: center; }
    </style>
  </head>
  <body>
    <div class="receipt">
      <div class="center">
        <div class="logo">${escapeHtml(logoText)}</div>
        <div class="brand">${escapeHtml(restaurantName)}</div>
        <div class="brand-line">${escapeHtml(restaurantLine)}</div>
        <div class="title">${escapeHtml(title)}</div>
      </div>
      <div class="divider"></div>
      ${metaRows}
      <div class="divider"></div>
      ${body}
      ${footer}
    </div>
  </body>
</html>`
}

export function printBill58mm({
  restaurantName,
  restaurantLine,
  logoText,
  orderNumber,
  tableLabel,
  floorLabel,
  orderType,
  printedAt,
  items,
  subtotal,
  taxAmount,
  grandTotal,
  notes,
}) {
  const metaRows = buildMetaRows([
    { label: 'Date', value: formatDateTime(printedAt) },
    { label: 'Order', value: orderNumber },
    { label: 'Table', value: tableLabel },
    { label: 'Floor', value: floorLabel },
    { label: 'Type', value: orderType },
  ])

  const body = (items ?? [])
    .map((item) => {
      const lineTotal = Number(item.lineTotal ?? item.quantity * item.unitPrice)
      return `
        <div class="line-item">
          <div class="line-name">
            <div>${escapeHtml(item.name)}</div>
            ${item.notes ? `<div class="note">Note: ${escapeHtml(item.notes)}</div>` : ''}
          </div>
          <div class="line-side">
            <div>${escapeHtml(item.quantity)} x ${formatMoney(item.unitPrice)}</div>
            <div>${formatMoney(lineTotal)}</div>
          </div>
        </div>
      `
    })
    .join('<div class="divider"></div>')

  const footer = `
    <div class="divider"></div>
    <div class="totals">
      <div class="total-row"><span>Subtotal</span><strong>${formatMoney(subtotal)}</strong></div>
      <div class="total-row"><span>Tax</span><strong>${formatMoney(taxAmount)}</strong></div>
      <div class="total-row grand-total"><span>Grand Total</span><strong>${formatMoney(grandTotal)}</strong></div>
    </div>
    ${notes ? `<div class="divider"></div><div class="note">Note: ${escapeHtml(notes)}</div>` : ''}
    <div class="divider"></div>
    <div class="footer">Thank you for visiting</div>
  `

  openPrintWindow(
    `Bill-${orderNumber ?? 'receipt'}`,
    buildShell({
      restaurantName,
      restaurantLine,
      logoText,
      title: 'Bill',
      metaRows,
      body,
      footer,
    }),
  )
}

export function printKot58mm({
  restaurantName,
  restaurantLine,
  logoText,
  orderNumber,
  tableLabel,
  floorLabel,
  orderType,
  printedAt,
  items,
  notes,
}) {
  const metaRows = buildMetaRows([
    { label: 'Date', value: formatDateTime(printedAt) },
    { label: 'Order', value: orderNumber },
    { label: 'Table', value: tableLabel },
    { label: 'Floor', value: floorLabel },
    { label: 'Type', value: orderType },
  ])

  const body = (items ?? [])
    .map((item) => `
      <div class="line-item">
        <div class="line-name">
          <div>${escapeHtml(item.name)}</div>
          ${item.notes ? `<div class="note">Note: ${escapeHtml(item.notes)}</div>` : ''}
        </div>
        <div class="line-side"><strong>${escapeHtml(item.quantity)}</strong></div>
      </div>
    `)
    .join('<div class="divider"></div>')

  const footer = `
    ${notes ? `<div class="divider"></div><div class="note">Note: ${escapeHtml(notes)}</div>` : ''}
    <div class="divider"></div>
    <div class="footer">Kitchen copy</div>
  `

  openPrintWindow(
    `KOT-${orderNumber ?? 'ticket'}`,
    buildShell({
      restaurantName,
      restaurantLine,
      logoText,
      title: 'KOT',
      metaRows,
      body,
      footer,
    }),
  )
}