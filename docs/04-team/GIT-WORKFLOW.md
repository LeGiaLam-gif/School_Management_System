# Quy trình Git

- `main` được khóa: **không push trực tiếp**, chỉ merge qua Pull Request đã qua CI và có ít nhất 1 review
  (`.github/CODEOWNERS`).
- Nhánh làm việc tách từ `main`, đặt tên `<loại>/<module>-<mô-tả-ngắn>`:
  `feat/enrollment-credit-limit`, `fix/grading-lock-check`, `docs/erd-update`, `chore/ci-cache`.
- Commit theo Conventional Commits: `feat(enrollment): add CreditLimitRule (BR-03)`; loại: `feat`, `fix`, `refactor`,
  `test`, `docs`, `chore`, `ci`. Dòng đầu ≤ 72 ký tự, nêu mã yêu cầu (FR-/BR-/NFR-) khi có.
- Một PR = một việc, ưu tiên nhỏ (dưới ~400 dòng đổi). Không trộn refactor với tính năng.
- Trước khi mở PR: `git fetch origin && git rebase origin/main`, chạy `composer ci` (Pint + Larastan + test).
- Merge bằng **squash**; tiêu đề squash theo Conventional Commits.
- Áp dụng bản patch kiến trúc: `git checkout -b feat/modular-architecture origin/main && git am <file>.patch`.
- Migration: không sửa migration đã merge vào `main`; thêm migration mới (timestamp = ngày tạo, nằm sau cùng).
- Không commit `.env`, `vendor/`, `node_modules/`, `public/build`.
- Giấy phép: chỉ tham khảo ý tưởng/cấu trúc từ repo GPL/AGPL, **không sao chép mã** (mục 12 requirements).
