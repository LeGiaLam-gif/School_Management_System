## Mục đích
<!-- PR này làm gì, liên quan yêu cầu nào (vd FR-ENR-02, BR-05)? -->

## Module ảnh hưởng
- [ ] Identity  - [ ] Academic  - [ ] Teaching  - [ ] Enrollment
- [ ] Attendance  - [ ] Grading  - [ ] Announcement  - [ ] Reporting  - [ ] Shared / hạ tầng

## Checklist
- [ ] Logic nghiệp vụ nằm ở Service (Controller mỏng, không truy vấn phức tạp trong Controller)
- [ ] Có Policy cho dữ liệu theo đối tượng (lecturer chỉ lớp mình, student chỉ dữ liệu của mình)
- [ ] Không vi phạm ranh giới module (`tests/Architecture` xanh)
- [ ] Có test (Unit cho rule/calculator, Feature cho HTTP + DB; 200/403 cho route mới)
- [ ] Thay đổi nhạy cảm (điểm, role, mở khóa, sửa điểm danh) có ghi audit
- [ ] `composer lint:fix` và `composer analyse` sạch
- [ ] Cập nhật `docs/04-team/progress.md` nếu đóng một mục sprint

## Ảnh chụp / ghi chú
