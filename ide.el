;;; Package --- Summary
;;; Commentary:
;;; It sets cmake-ide variables

;;; Code:
(let-alist projects
  (if (eq .FerryFair 't)
      (progn (message "FerryFair already loaded"))
    (progn
      (setq projects '((FerryFair . 't)))
      (setq CMakeProject "FerryFair")

      (setq cmake-ide-build-dir
            (concat (file-name-directory (buffer-file-name))
                    "build/Linux/x86_64/debug/"))
      (setq cmake-ide-build-pool-dir cmake-ide-build-dir)
      (make-directory cmake-ide-build-dir t)
      (setq cmake-ide-cmake-args
            (concat "-D_DEBUG=1 -DCMAKE_BUILD_TYPE=Debug "
	                 "-DCMAKE_EXPORT_COMPILE_COMMANDS=1 "
                    "-DBUILD_TESTING=1"))
      (setq MakeThreadCount 6)
      (setq DebugTarget "FerryFair")
      (setq TargetArguments "-s normal")
      (message "FerryFair emacs project loaded."))))

(provide 'FerryFair)
;;; emacs.el ends here
