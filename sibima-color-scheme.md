# SIBIMA Web Color Scheme Documentation

## Overview

Dokumen ini mendefinisikan color palette utama yang digunakan dalam pengembangan sistem SIBIMA (Sistem Bimbingan Mahasiswa). Skema warna ini dirancang untuk mencerminkan kesan profesional, modern, dan mendukung konteks edukasi berbasis teknologi.

---

## 1. Brand Colors

### Primary Colors

| Name      | Hex     | Usage                          |
| --------- | ------- | ------------------------------ |
| Deep Blue | #1F4C7A | Navbar, header, primary button |
| Navy Blue | #1B3F66 | Hover state, footer, sidebar   |

---

## 2. Secondary Colors

| Name       | Hex     | Usage                          |
| ---------- | ------- | ------------------------------ |
| Teal       | #2FA7A0 | Links, icons, accent UI        |
| Light Teal | #6CCBC3 | Section background, highlights |

---

## 3. Accent Colors

| Name          | Hex     | Usage                 |
| ------------- | ------- | --------------------- |
| Orange        | #F39C12 | CTA button, alerts    |
| Golden Yellow | #F4B942 | Hover CTA, highlights |

---

## 4. Additional Accent

| Name       | Hex     | Usage                              |
| ---------- | ------- | ---------------------------------- |
| Leaf Green | #6DBE45 | Success state, progress indicators |

---

## 5. Neutral Colors

| Name        | Hex     | Usage          |
| ----------- | ------- | -------------- |
| Dark Gray   | #2F2F2F | Primary text   |
| Medium Gray | #7A7A7A | Secondary text |
| Light Gray  | #F5F7FA | Background     |
| White       | #FFFFFF | Base UI        |

---

## 6. Recommended UI Mapping

### Layout

- Background: #F5F7FA
- Surface/Card: #FFFFFF

### Typography

- Heading: #1B3F66
- Body Text: #2F2F2F
- Caption: #7A7A7A

### Buttons

#### Primary Button

- Background: #1F4C7A
- Text: #FFFFFF
- Hover: #1B3F66

#### Secondary Button

- Background: #2FA7A0
- Text: #FFFFFF

#### CTA Button

- Background: #F39C12
- Text: #FFFFFF

---

## 7. Status Colors

| Status  | Color   |
| ------- | ------- |
| Success | #6DBE45 |
| Warning | #F39C12 |
| Info    | #2FA7A0 |

---

## 8. Accessibility Notes

- Gunakan kontras minimum WCAG 2.1
- Jangan gunakan warna sebagai satu-satunya indikator status

---

## 9. CSS Variables

```css
:root {
    --color-primary: #1f4c7a;
    --color-primary-dark: #1b3f66;
    --color-secondary: #2fa7a0;
    --color-secondary-light: #6ccbc3;
    --color-accent: #f39c12;
    --color-accent-light: #f4b942;
    --color-success: #6dbe45;

    --color-text-primary: #2f2f2f;
    --color-text-secondary: #7a7a7a;

    --color-bg: #f5f7fa;
    --color-white: #ffffff;
}
```

---

## 10. Notes for Developers

- Gunakan variabel warna
- Hindari hardcoded color
- Jaga konsistensi antar halaman
