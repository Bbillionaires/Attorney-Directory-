const multer = require('multer');

const ALLOWED_MIMETYPES = new Set(['image/jpeg', 'image/png', 'application/pdf']);
const ALLOWED_IMAGE_MIMETYPES = new Set(['image/jpeg', 'image/png']);

const upload = multer({
  storage: multer.memoryStorage(),
  limits: { fileSize: 8 * 1024 * 1024 },
  fileFilter(req, file, cb) {
    if (!ALLOWED_MIMETYPES.has(file.mimetype)) {
      return cb(new Error('Unsupported file type. Only JPEG, PNG, and PDF are allowed.'));
    }
    cb(null, true);
  },
});

const uploadImage = multer({
  storage: multer.memoryStorage(),
  limits: { fileSize: 4 * 1024 * 1024 },
  fileFilter(req, file, cb) {
    if (!ALLOWED_IMAGE_MIMETYPES.has(file.mimetype)) {
      return cb(new Error('Unsupported file type. Only JPEG and PNG are allowed.'));
    }
    cb(null, true);
  },
});

module.exports = { upload, uploadImage };
