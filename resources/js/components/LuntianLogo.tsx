type LuntianLogoProps = {
  compact?: boolean;
  className?: string;
};

export default function LuntianLogo({ compact = false, className = '' }: LuntianLogoProps) {
  if (compact) {
    return (
      <span
        className={`inline-block text-lg font-extrabold tracking-[0.14em] text-[#f5a623] sm:text-xl ${className}`}
        role="img"
        aria-label="Luntian"
      >
        LUNTIAN
      </span>
    );
  }

  return (
    <div
      className={`mx-auto w-full max-w-[280px] text-center ${className}`}
      role="img"
      aria-label="Luntian Residential Building Design Solutions"
    >
      <div className="text-[1.85rem] font-extrabold leading-none tracking-[0.14em] text-[#f5a623] sm:text-[2.15rem]">
        LUNTIAN
      </div>
      <div className="mt-2 text-[0.65rem] font-medium tracking-[0.04em] text-slate-500 sm:text-[0.7rem] dark:text-slate-400">
        Residential Building Design Solutions
      </div>
      <div className="mt-2.5 bg-[#f5a623] px-2 py-1.5 text-[0.55rem] font-semibold uppercase tracking-[0.12em] text-white sm:text-[0.6rem]">
        • Energy • Building Design • VR • AR
      </div>
    </div>
  );
}
