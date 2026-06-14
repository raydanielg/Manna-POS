import { createFileRoute, redirect, useNavigate } from "@tanstack/react-router";
import { useAuth } from "@/lib/auth-context";
import { useState, useEffect, useRef } from "react";

export const Route = createFileRoute("/login")({
  component: LoginPage,
});

// ── Toast ────────────────────────────────────────────────
type ToastType = "success" | "error" | "info";
interface Toast { id: number; message: string; type: ToastType }

function useToast() {
  const [toasts, setToasts] = useState<Toast[]>([]);
  const counter = useRef(0);

  const add = (message: string, type: ToastType = "success") => {
    const id = ++counter.current;
    setToasts(prev => [...prev, { id, message, type }]);
    setTimeout(() => setToasts(prev => prev.filter(t => t.id !== id)), 4500);
  };

  return { toasts, add };
}

function ToastContainer({ toasts }: { toasts: Toast[] }) {
  if (!toasts.length) return null;
  const icons = {
    success: (
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5} className="w-4 h-4">
        <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
      </svg>
    ),
    error: (
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5} className="w-4 h-4">
        <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    ),
    info: (
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5} className="w-4 h-4">
        <path strokeLinecap="round" strokeLinejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    ),
  };
  const bg = { success: "bg-emerald-50 border-emerald-200 text-emerald-800", error: "bg-red-50 border-red-200 text-red-800", info: "bg-blue-50 border-blue-200 text-blue-800" };
  const ic = { success: "bg-emerald-100 text-emerald-600", error: "bg-red-100 text-red-600", info: "bg-blue-100 text-blue-600" };

  return (
    <div className="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none">
      {toasts.map(t => (
        <div
          key={t.id}
          className={`flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg text-sm font-medium min-w-64 max-w-sm pointer-events-auto animate-in slide-in-from-right-4 fade-in duration-300 ${bg[t.type]}`}
        >
          <span className={`flex items-center justify-center w-7 h-7 rounded-lg flex-shrink-0 ${ic[t.type]}`}>
            {icons[t.type]}
          </span>
          <span>{t.message}</span>
        </div>
      ))}
    </div>
  );
}

// ── Role-based redirect ──────────────────────────────────
function getDashboardPath(role: string): string {
  switch (role) {
    case "admin":
    case "manager":
      return "/dashboard";
    case "cashier":
      return "/dashboard";
    default:
      return "/dashboard";
  }
}

// ── Login Page ───────────────────────────────────────────
function LoginPage() {
  const { login, isAuthenticated, isLoading: authLoading, user } = useAuth();
  const navigate = useNavigate();
  const { toasts, add: addToast } = useToast();

  const [email, setEmail]       = useState("");
  const [password, setPassword] = useState("");
  const [showPw, setShowPw]     = useState(false);
  const [remember, setRemember] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  const [emailErr, setEmailErr]     = useState("");
  const [passwordErr, setPasswordErr] = useState("");
  const [generalErr, setGeneralErr] = useState("");

  useEffect(() => {
    if (isAuthenticated && user) {
      navigate({ to: getDashboardPath(user.role) as any });
    }
  }, [isAuthenticated, user, navigate]);

  const validateForm = (): boolean => {
    let ok = true;
    setEmailErr(""); setPasswordErr(""); setGeneralErr("");

    if (!email.trim()) {
      setEmailErr("Email address is required"); ok = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) {
      setEmailErr("Please enter a valid email address"); ok = false;
    }
    if (!password) {
      setPasswordErr("Password is required"); ok = false;
    } else if (password.length < 6) {
      setPasswordErr("Password must be at least 6 characters"); ok = false;
    }
    return ok;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validateForm()) return;

    setIsLoading(true);
    try {
      await login({ email: email.trim(), password });
      addToast(`Welcome back! You are now signed in.`, "success");
      setTimeout(() => {
        const role = (window as any).__authUser?.role || "user";
        navigate({ to: getDashboardPath(role) as any });
      }, 800);
    } catch (err: any) {
      const msg = err?.message || "Login failed. Please check your credentials.";
      if (msg.toLowerCase().includes("password")) {
        setPasswordErr(msg);
      } else if (msg.toLowerCase().includes("email") || msg.toLowerCase().includes("credential")) {
        setGeneralErr(msg);
      } else {
        setGeneralErr(msg);
      }
      addToast(msg, "error");
    } finally {
      setIsLoading(false);
    }
  };

  if (authLoading) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100">
        <div className="text-center">
          <svg className="h-8 w-8 animate-spin text-emerald-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeOpacity="0.25" strokeWidth="3"/>
            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity="0.75"/>
          </svg>
          <p className="text-sm text-slate-500">Loading…</p>
        </div>
      </div>
    );
  }

  return (
    <>
      <ToastContainer toasts={toasts} />

      <div className="flex min-h-screen bg-white">

        {/* ── Left brand panel ── */}
        <div className="hidden lg:flex flex-1 flex-col justify-between p-10 bg-gradient-to-br from-emerald-50 via-white to-teal-50 relative overflow-hidden">
          <div className="relative z-10">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-extrabold text-lg shadow-lg shadow-emerald-200">
                M
              </div>
              <span className="font-extrabold text-slate-900 tracking-tight text-lg">MannaPOS</span>
            </div>
          </div>

          {/* Center illustration area */}
          <div className="relative z-10 flex-1 flex items-center justify-center py-12">
            <div className="text-center max-w-sm">
              <div className="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-emerald-300">
                <svg className="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016 2.993 2.993 0 002.25-1.016 3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
                </svg>
              </div>
              <h2 className="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Manage your business</h2>
              <p className="text-slate-500 text-sm leading-relaxed">Sales, inventory, customers and reports — all in one powerful dashboard.</p>

              <div className="mt-8 grid grid-cols-3 gap-3">
                {[
                  { label: "Sales", icon: "💰" },
                  { label: "Inventory", icon: "📦" },
                  { label: "Reports", icon: "📊" },
                ].map(item => (
                  <div key={item.label} className="bg-white/70 backdrop-blur-sm rounded-xl p-3 border border-white shadow-sm text-center">
                    <div className="text-xl mb-1">{item.icon}</div>
                    <div className="text-xs font-semibold text-slate-600">{item.label}</div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          <div className="relative z-10 text-xs text-slate-400 text-center">
            © {new Date().getFullYear()} MannaPOS. All rights reserved.
          </div>

          {/* Decorative blobs */}
          <div className="absolute -top-24 -right-24 w-96 h-96 bg-emerald-100 rounded-full blur-3xl opacity-60 pointer-events-none" />
          <div className="absolute -bottom-32 -left-16 w-80 h-80 bg-teal-100 rounded-full blur-3xl opacity-50 pointer-events-none" />
        </div>

        {/* ── Right form panel ── */}
        <div className="w-full lg:w-[480px] flex flex-col justify-center px-8 py-12 lg:px-14 bg-white lg:border-l lg:border-slate-100">

          {/* Mobile logo */}
          <div className="flex items-center gap-3 mb-8 lg:hidden">
            <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-extrabold shadow-lg">M</div>
            <span className="font-extrabold text-slate-900 text-lg">MannaPOS</span>
          </div>

          <div className="mb-8">
            <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight mb-1">Welcome back</h1>
            <p className="text-sm text-slate-500">Sign in to your account to continue</p>
          </div>

          {/* General credentials error */}
          {generalErr && (
            <div className="flex items-start gap-3 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium mb-5 animate-in slide-in-from-top-2 duration-300">
              <span className="flex items-center justify-center w-7 h-7 rounded-lg bg-red-100 flex-shrink-0 mt-0.5">
                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
              </span>
              <div>
                <div className="font-semibold text-red-800 text-xs mb-0.5">Login Failed</div>
                {generalErr}
              </div>
            </div>
          )}

          <form onSubmit={handleSubmit} noValidate className="space-y-5">

            {/* Email */}
            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wide">
                Email Address
              </label>
              <div className="relative">
                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.8}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                  </svg>
                </span>
                <input
                  type="email"
                  value={email}
                  onChange={e => { setEmail(e.target.value); setEmailErr(""); setGeneralErr(""); }}
                  placeholder="name@company.com"
                  disabled={isLoading}
                  className={`w-full pl-9 pr-4 py-3 text-sm rounded-xl border bg-slate-50 text-slate-900 placeholder:text-slate-400 outline-none transition-all duration-150 ${
                    emailErr
                      ? "border-red-400 bg-red-50 ring-1 ring-red-300"
                      : "border-slate-200 focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                  }`}
                />
              </div>
              {emailErr && (
                <p className="flex items-center gap-1.5 text-xs text-red-600 font-semibold mt-1.5 animate-in slide-in-from-top-1 duration-200">
                  <svg className="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                  </svg>
                  {emailErr}
                </p>
              )}
            </div>

            {/* Password */}
            <div>
              <div className="flex items-center justify-between mb-1.5">
                <label className="block text-xs font-semibold text-slate-700 uppercase tracking-wide">Password</label>
                <a href="#" className="text-xs text-emerald-600 font-semibold hover:text-emerald-700 hover:underline transition-colors">
                  Forgot password?
                </a>
              </div>
              <div className="relative">
                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.8}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                  </svg>
                </span>
                <input
                  type={showPw ? "text" : "password"}
                  value={password}
                  onChange={e => { setPassword(e.target.value); setPasswordErr(""); setGeneralErr(""); }}
                  placeholder="••••••••"
                  disabled={isLoading}
                  className={`w-full pl-9 pr-11 py-3 text-sm rounded-xl border bg-slate-50 text-slate-900 placeholder:text-slate-400 outline-none transition-all duration-150 ${
                    passwordErr
                      ? "border-red-400 bg-red-50 ring-1 ring-red-300"
                      : "border-slate-200 focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                  }`}
                />
                <button
                  type="button"
                  onClick={() => setShowPw(v => !v)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                  tabIndex={-1}
                >
                  {showPw ? (
                    <svg className="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.8}>
                      <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                      <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                  ) : (
                    <svg className="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.8}>
                      <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                  )}
                </button>
              </div>
              {passwordErr && (
                <p className="flex items-center gap-1.5 text-xs text-red-600 font-semibold mt-1.5 animate-in slide-in-from-top-1 duration-200">
                  <svg className="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                  </svg>
                  {passwordErr}
                </p>
              )}
            </div>

            {/* Remember me */}
            <label className="flex items-center gap-2.5 cursor-pointer w-fit select-none">
              <div
                onClick={() => setRemember(v => !v)}
                className={`w-4.5 h-4.5 rounded-[5px] border-2 flex items-center justify-center flex-shrink-0 transition-all ${
                  remember ? "bg-emerald-500 border-emerald-500" : "border-slate-300 bg-white"
                }`}
              >
                {remember && (
                  <svg className="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={3.5}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                  </svg>
                )}
              </div>
              <span className="text-sm text-slate-600 font-medium">Remember me for 30 days</span>
            </label>

            {/* Submit button */}
            <button
              type="submit"
              disabled={isLoading}
              className="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 shadow-lg shadow-emerald-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-emerald-200 active:translate-y-0 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0"
            >
              {isLoading ? (
                <>
                  <svg className="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" strokeOpacity="0.3" strokeWidth="3"/>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity="0.8"/>
                  </svg>
                  Signing in…
                </>
              ) : (
                <>
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                  </svg>
                  Sign In
                </>
              )}
            </button>
          </form>

          <div className="flex items-center gap-3 my-6 text-slate-300 text-xs">
            <div className="flex-1 h-px bg-slate-100" />
            <span className="text-slate-400">or</span>
            <div className="flex-1 h-px bg-slate-100" />
          </div>

          <p className="text-center text-sm text-slate-500">
            Don't have an account?{" "}
            <a href="#" className="text-emerald-600 font-bold hover:text-emerald-700 hover:underline transition-colors">
              Create account
            </a>
          </p>

        </div>
      </div>
    </>
  );
}
