<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Bhojnalaya API",
 *     version="1.0.0",
 *     description="REST API documentation for tables, menu items, orders, and order items."
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local development server"
 * )
 *
 * @OA\Tag(name="Tables", description="Restaurant dining table management")
 * @OA\Tag(name="Menu Items", description="Restaurant menu catalog")
 * @OA\Tag(name="Orders", description="Order creation and billing flow")
 * @OA\Tag(name="Order Items", description="Line items inside an order")
 *
 * @OA\Schema(
 *     schema="Table",
 *     type="object",
 *     required={"id","name","capacity","status","is_active"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="T1"),
 *     @OA\Property(property="floor", type="string", nullable=true, example="Ground Floor"),
 *     @OA\Property(property="capacity", type="integer", example=4),
 *     @OA\Property(property="status", type="string", example="blank"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-15T10:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-04-15T10:00:00.000000Z")
 * )
 *
 * @OA\Schema(
 *     schema="TableRequest",
 *     type="object",
 *     required={"name","capacity","status"},
 *     @OA\Property(property="name", type="string", example="T1"),
 *     @OA\Property(property="floor", type="string", example="Ground Floor"),
 *     @OA\Property(property="capacity", type="integer", example=4),
 *     @OA\Property(property="status", type="string", example="blank"),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="MenuItem",
 *     type="object",
 *     required={"id","name","category","item_type","price","is_available"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Paneer Tikka"),
 *     @OA\Property(property="category", type="string", example="Starters"),
 *     @OA\Property(property="short_code", type="string", nullable=true, example="PT01"),
 *     @OA\Property(property="item_type", type="string", example="Veg"),
 *     @OA\Property(property="price", type="number", format="float", example=220),
 *     @OA\Property(property="is_available", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-15T10:05:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-04-15T10:05:00.000000Z")
 * )
 *
 * @OA\Schema(
 *     schema="MenuItemRequest",
 *     type="object",
 *     required={"name","category","item_type","price"},
 *     @OA\Property(property="name", type="string", example="Paneer Tikka"),
 *     @OA\Property(property="category", type="string", example="Starters"),
 *     @OA\Property(property="short_code", type="string", example="PT01"),
 *     @OA\Property(property="item_type", type="string", example="Veg"),
 *     @OA\Property(property="price", type="number", format="float", example=220),
 *     @OA\Property(property="is_available", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="OrderItem",
 *     type="object",
 *     required={"id","order_id","menu_item_id","quantity","unit_price","line_total"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="menu_item_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="unit_price", type="number", format="float", example=220),
 *     @OA\Property(property="line_total", type="number", format="float", example=440),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Less spicy"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-15T10:15:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-04-15T10:15:00.000000Z")
 * )
 *
 * @OA\Schema(
 *     schema="OrderItemRequest",
 *     type="object",
 *     required={"order_id","menu_item_id","quantity"},
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="menu_item_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="notes", type="string", example="Less spicy")
 * )
 *
 * @OA\Schema(
 *     schema="NestedOrderItemRequest",
 *     type="object",
 *     required={"menu_item_id","quantity"},
 *     @OA\Property(property="menu_item_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="notes", type="string", example="No onion")
 * )
 *
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     required={"id","order_number","order_type","status","subtotal","tax_amount","total"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="table_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="order_number", type="string", example="ORD-1001"),
 *     @OA\Property(property="order_type", type="string", example="Dine In"),
 *     @OA\Property(property="status", type="string", example="open"),
 *     @OA\Property(property="subtotal", type="number", format="float", example=440),
 *     @OA\Property(property="tax_amount", type="number", format="float", example=20),
 *     @OA\Property(property="total", type="number", format="float", example=460),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Serve quickly"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-15T10:20:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-04-15T10:20:00.000000Z"),
 *     @OA\Property(property="table", ref="#/components/schemas/Table"),
 *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/OrderItem"))
 * )
 *
 * @OA\Schema(
 *     schema="OrderRequest",
 *     type="object",
 *     required={"order_number","order_type"},
 *     @OA\Property(property="table_id", type="integer", example=1),
 *     @OA\Property(property="order_number", type="string", example="ORD-1001"),
 *     @OA\Property(property="order_type", type="string", example="Dine In"),
 *     @OA\Property(property="status", type="string", example="open"),
 *     @OA\Property(property="tax_amount", type="number", format="float", example=20),
 *     @OA\Property(property="notes", type="string", example="Less spicy"),
 *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/NestedOrderItemRequest"))
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required."))
 *     )
 * )
 */
class OpenApiSpec
{
}
