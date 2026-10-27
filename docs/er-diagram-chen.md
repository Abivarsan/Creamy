# Delivergate Ice Cream Shop Management System - ER Diagram (Chen Notation)

## 1. Overview

This document presents the **Conceptual Entity-Relationship (ER) Model** for the Delivergate Ice Cream Shop backend using **Peter Chen's Notation (1976)**. 

While the Crow's Foot notation ([`docs/er-diagram.svg`](er-diagram.svg) and [`docs/er-diagram.md`](er-diagram.md)) models the physical relational database schema (tables, foreign keys, and indexes), Chen's notation models the domain at the **conceptual level**, highlighting:
- **Entities** as real-world objects or concepts.
- **Attributes** as properties describing entities.
- **Relationships** as named semantic associations between entities.
- **Cardinality Ratios** specifying mapping constraints (`1:1`, `1:N`, `M:N`).

> 💡 **Vector Diagram Asset**:  
> A high-resolution, presentation-ready vector graphic of this Chen diagram is available at:  
> 🔗 **[`docs/er-diagram-chen.svg`](er-diagram-chen.svg)**

---

## 2. Comparison: Chen Notation vs. Crow's Foot Notation

| Visual Element | Chen Notation (Conceptual Model) | Crow's Foot Notation (Relational Schema) |
| :--- | :--- | :--- |
| **Primary Focus** | Conceptual data semantics & business rules | Physical database design & table structures |
| **Entities** | **Rectangles** with entity name only (e.g., `USER`) | Multi-row table boxes with columns and data types |
| **Weak Entities** | **Double-bordered Rectangles** (e.g., `PERSONAL_ACCESS_TOKEN`) | Child table containing a non-null Foreign Key |
| **Relationships** | **Diamonds** containing action verbs (`ISSUES`, `MANAGES`, `SERVES`) | Direct connector lines between tables |
| **Attributes** | **Ellipses / Ovals** radiating outward from entities | Listed as rows inside the table box |
| **Primary Keys (PK)** | **Underlined text** inside the ellipse (e.g., <u>`id`</u>) | Annotated with `PK` tag |
| **Unique Keys (UK)** | Dashed ellipse border or `(UK)` label | Annotated with `UK` or unique constraint |
| **Cardinality** | Numeric / variable labels on connectors (`1`, `N`, `M`) | Visual crow's feet and crossbar icons (`||`, `|{`, `o{`) |

---

## 3. Conceptual Entities & Attributes

### 3.1 Strong Entities

#### 1. `USER`
Represents internal shop staff and stakeholders authenticated by the system.
- <u>`id`</u>: Primary identifier (PK).
- `name`: Staff member's full name.
- `email`: Login email address (Unique Candidate Key `[UK]`).
- `password`: Bcrypt hashed credential.
- `role`: Role-Based Access Control tier (`owner`, `manager`, `cashier`).
- `remember_token`: Session persistence token.
- `created_at` / `updated_at`: System audit timestamps.

#### 2. `ITEM`
Represents the ice cream products and inventory items sold by the store.
- <u>`id`</u>: Primary identifier (PK).
- `name`: Flavor name / product title (Indexed).
- `flavor_category`: Grouping category (e.g., Chocolate, Vanilla, Fruit) (Indexed).
- `price`: Unit price per scoop, cone, or tub.
- `quantity`: Current inventory stock level.
- `unit`: Measurement unit (`scoop`, `tub`, `cone`, `liter`).
- `description`: Flavor notes and allergens.
- `is_active`: Availability flag for active sales.

#### 3. `CUSTOMER`
Represents customers whose profiles and contact details are maintained for sales and loyalty tracking.
- <u>`id`</u>: Primary identifier (PK).
- `name`: Customer's full name (Indexed).
- `email`: Contact email address (Indexed).
- `phone`: Primary contact number (Indexed).
- `address`: Delivery or billing address.
- `notes`: Order preferences, dietary restrictions, or notes.
- `created_at` / `updated_at`: Audit timestamps.

### 3.2 Weak Entities & Identifying Relationships

#### 4. `PERSONAL_ACCESS_TOKEN` (Weak Entity)
Represents API authentication tokens issued via Laravel Sanctum.
- **Why Weak Entity?** Tokens have no independent existence outside of the authenticated `USER` that issued them; if a `USER` is deleted, all their associated tokens are cascaded and destroyed.
- **Identifying Relationship**: `<ISSUES>` (Double-bordered diamond connected to `USER`).
- **Attributes**:
  - <u>`id`</u>: Primary key.
  - `tokenable_type`: Polymorphic model class (`App\Models\User`).
  - `tokenable_id`: Identifying Foreign Key referencing `USER.id`.
  - `name`: Token descriptor (e.g., `api-token`).
  - `token`: SHA-256 hashed token string (`[UK]`).
  - `abilities`: JSON serialized scopes and permissions.
  - `last_used_at` / `expires_at`: Token lifecycle timestamps.

---

## 4. Semantic Relationships & Cardinality

```text
[ USER ] ──(1)──< ISSUES >──(N)──[[ PERSONAL_ACCESS_TOKEN ]]
   │
  (M)
   │
< MANAGES >
   │
  (N)
   │
[ ITEM ] ──(N)──< BUYS >──(M)──[ CUSTOMER ]
```

1. **`USER` (1) --- `<ISSUES>` --- (N) `PERSONAL_ACCESS_TOKEN`**:
   - **Type**: 1-to-Many identifying relationship.
   - **Semantics**: One staff member can hold multiple active API tokens across mobile, desktop, or tablet sessions; each token belongs strictly to one user.

2. **`USER` (M) --- `<MANAGES>` --- (N) `ITEM`**:
   - **Type**: Many-to-Many administrative relationship.
   - **Semantics**: Owners and Managers create, update, and manage inventory items. Cashiers update stock levels upon sales. Multiple staff members manage multiple items.

3. **`USER` (1) --- `<SERVES>` --- (N) `CUSTOMER`**:
   - **Type**: 1-to-Many administrative relationship.
   - **Semantics**: Staff members record and look up customer profiles during point-of-sale transactions.

4. **`CUSTOMER` (M) --- `<BUYS>` --- (N) `ITEM`**:
   - **Type**: Many-to-Many domain relationship.
   - **Semantics**: Conceptual business relationship where customers purchase multiple ice cream flavors, and each flavor is purchased by multiple customers.

---

## 5. Step-by-Step Guide: Drawing in draw.io (diagrams.net)

If you need to manually reproduce or customize this Chen ER diagram using [draw.io](https://app.diagrams.net), follow these exact instructions:

### Step 1: Open draw.io & Enable the Chen Shape Library
1. Navigate to **[app.diagrams.net](https://app.diagrams.net)** in your web browser.
2. Select **Create New Diagram** -> Choose **Blank Diagram** -> Click **Create**.
3. In the left-hand sidebar, scroll to the bottom and click **+ More Shapes**.
4. Check the box for **Entity Relation** (under "Software").
5. Click **Apply**. You will now see standard ER shapes (Entity, Weak Entity, Relationship, Attribute, Key Attribute) in your sidebar.

### Step 2: Configure Page & Canvas Settings
1. In the right-hand panel, under **Diagram**:
   - Set **Paper Size** to `A3` (Landscape) or custom `1400 x 960 px` to give ample space for attribute branches.
   - Check **Grid** (Grid Size: `10 pt`) to easily snap and align shapes.

### Step 3: Place the Entity Shapes (Rectangles)
1. Drag four **Entity** rectangles onto the canvas:
   - **USER**: Position top-left (e.g., `X: 250, Y: 300`, Size: `160 x 50 pt`).
   - **PERSONAL_ACCESS_TOKEN**: Position bottom-left (e.g., `X: 220, Y: 650`, Size: `220 x 50 pt`).  
     *Tip: Use the **Weak Entity** shape (double-lined rectangle) from the Entity Relation library.*
   - **ITEM**: Position top-right (e.g., `X: 880, Y: 300`, Size: `160 x 50 pt`).
   - **CUSTOMER**: Position bottom-right (e.g., `X: 880, Y: 650`, Size: `160 x 50 pt`).
2. Label each box with uppercase text (`USER`, `PERSONAL_ACCESS_TOKEN`, `ITEM`, `CUSTOMER`).

### Step 4: Add Relationship Shapes (Diamonds)
1. Drag a **Relationship** (Diamond) shape between connected entities:
   - Between `USER` and `PERSONAL_ACCESS_TOKEN`: Label it `ISSUES` (Use **Weak Relationship** double-diamond if available).
   - Between `USER` and `ITEM`: Label it `MANAGES`.
   - Between `USER` and `CUSTOMER`: Label it `SERVES`.
   - Between `ITEM` and `CUSTOMER`: Label it `BUYS`.
2. Connect entities to relationships using standard connector lines:
   - Click an entity's connection point and drag to the diamond.
   - In the right-hand panel, set **Line Style** to **Straight** or **Orthogonal**.

### Step 5: Add Cardinality Ratio Labels
1. Double-click on or near the connector lines to add text labels:
   - `USER` to `ISSUES`: `1`
   - `ISSUES` to `PERSONAL_ACCESS_TOKEN`: `N`
   - `USER` to `MANAGES`: `M`
   - `MANAGES` to `ITEM`: `N`
   - `USER` to `SERVES`: `1`
   - `SERVES` to `CUSTOMER`: `N`
   - `ITEM` to `BUYS`: `N`
   - `BUYS` to `CUSTOMER`: `M`

### Step 6: Add Attribute Ellipses & Underline Primary Keys
1. Drag **Attribute** (Ellipse / Oval) shapes around each entity:
   - **For `USER`**: Place ellipses for `id`, `name`, `email`, `password`, `role`, `remember_token`, `created_at`.
   - **For `ITEM`**: Place ellipses for `id`, `name`, `flavor_category`, `price`, `quantity`, `unit`, `description`, `is_active`.
   - **For `CUSTOMER`**: Place ellipses for `id`, `name`, `email`, `phone`, `address`, `notes`.
   - **For `PERSONAL_ACCESS_TOKEN`**: Place ellipses for `id`, `tokenable_id`, `tokenable_type`, `name`, `token`, `abilities`, `last_used_at`.
2. **Designate Primary Keys**:
   - Double-click the text `id` inside the primary key ellipses.
   - Highlight the text and press **Ctrl + U** (or Cmd + U on macOS) to apply the formal Chen notation **underline**: <u>`id`</u>.
3. **Designate Unique Keys**:
   - For `email` on `USER` and `token` on `PERSONAL_ACCESS_TOKEN`, select the ellipse and set **Line Style** to **Dashed** (`Dashed: 3 3`) or append `(UK)`.
4. Connect each ellipse to its parent entity with a straight thin line.

### Step 7: Exporting Your Diagram
1. Go to **File** -> **Export as**.
2. Select your desired format:
   - **SVG**: Ideal for crisp, zoomable rendering in web and markdown repositories.
   - **PNG**: Best for pasting into Word, Google Docs, or slides (set DPI to `300` for high resolution).
   - **PDF**: Ideal for formal technical documentation and email submissions.
3. Save the exported file as `er-diagram-chen.svg` or `er-diagram-chen.png`.
