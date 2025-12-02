curl -X POST http://127.0.0.1:8000/api/get-token \
-H "Content-Type: application/json" \
-d '{"name":"user_test", "date_request":"2025-11-25 17:02:00"}'

curl -X POST http://127.0.0.1:8000/api/get-data \
-H "Content-Type: application/json" \
-H 'secretKey: Qw3rty09!@#' \
-H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsYXJhdmVsLWFwaSIsInN1YiI6InVzZXJfdGVzdCIsImlhdCI6MTc2NDQwNDc3OCwiZXhwIjoxNzY0NDA4Mzc4fQ.GU-sxXQG_lO_0IvQpXnCRzMCYuCevrNu568lzpSfC5c" \
-d '{"name_customers":"jonatan christie", "date_request":"2025-11-25 17:02:00"}'

=========================================================

{"result":[{"name_customers":"jonatan christie","items":"Lampu bohlam LED 20 WATT","dicount":"0,02","fix_price":"19600"},{"name_customers":"jonatan christie","items":"Mouse wireless logitech","dicount":"0,035","fix_price":"168875"}]}ridzi@ridzi-dev:~/app-php/test_vads$ 